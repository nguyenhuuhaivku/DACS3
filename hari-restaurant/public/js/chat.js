const chatMessages = document.getElementById("chatMessages");
const messageInput = document.getElementById("messageInput");

// Khởi tạo Pusher
const pusher = new Pusher("your_app_key", {
    cluster: "ap1",
    encrypted: true,
});

// Subscribe to private channel
const channel = pusher.subscribe(`private-chat.${conversationId}`);

// Listen for new messages
channel.bind("new-message", function (data) {
    appendMessage(data.message);

    // Cập nhật danh sách hội thoại (nếu cần)
    const conversationLink = document.querySelector(
        `a[href$="/${data.message.conversation_id}"]`
    );
    if (conversationLink) {
        const badge = conversationLink.querySelector(".badge");
        if (badge) {
            badge.textContent = parseInt(badge.textContent || "0") + 1;
        }
    }
});

async function sendMessage() {
    const input = document.getElementById("messageInput");
    const message = input.value.trim();

    if (message) {
        try {
            const response = await fetch("/admin/support/reply/" + conversationId, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                },
                body: JSON.stringify({ message }),
            });

            const result = await response.json();

            if (response.ok && result.success) {
                appendMessage(result.message);
                input.value = "";
            } else {
                console.error("Error:", result);
                alert("Không thể gửi tin nhắn. Vui lòng thử lại.");
            }
        } catch (error) {
            console.error("Fetch Error:", error);
            alert("Có lỗi xảy ra. Vui lòng thử lại.");
        }
    }
}

function appendMessage(message) {
    const messageDiv = document.createElement("div");
    messageDiv.className = `message ${message.sender_id === userId ? "sent" : "received"}`;
    messageDiv.innerHTML = `
        <p>${message.message}</p>
        <span class="time">${new Date(message.created_at).toLocaleTimeString()}</span>
    `;
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Load existing messages
fetch("/chat/messages")
    .then((response) => response.json())
    .then((messages) => {
        messages.forEach((message) => appendMessage(message));
    });
