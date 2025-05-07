document.addEventListener("DOMContentLoaded", function () {
    initializeSliders();
    initializeImages();
    initializeAddToCartButtons();

    // Thêm event listener cho các category tab
    document.querySelectorAll(".category-tab").forEach((tab) => {
        tab.addEventListener("click", function () {
            const category = this.dataset.category;
            changeCategory(category);
        });
    });

    // Kích hoạt category đầu tiên
    const firstCategory = document.querySelector(".category-tab");
    if (firstCategory) {
        changeCategory(firstCategory.dataset.category);
    }
});

function initializeSliders() {
    document.querySelectorAll(".dishes-slider").forEach((slider) => {
        const slidesContainer = slider.querySelector(".slides-container");
        const slides = slidesContainer.children;
        const slideWidth = slides[0].offsetWidth;
        let currentIndex = 0;

        // Previous button
        slider.querySelector(".prev").addEventListener("click", () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateSliderPosition();
            }
        });

        // Next button
        slider.querySelector(".next").addEventListener("click", () => {
            if (currentIndex < slides.length - getVisibleSlides()) {
                currentIndex++;
                updateSliderPosition();
            }
        });

        function updateSliderPosition() {
            const offset = -currentIndex * slideWidth;
            slidesContainer.style.transform = `translateX(${offset}px)`;
        }

        function getVisibleSlides() {
            const sliderWidth = slider.offsetWidth;
            return Math.floor(sliderWidth / slideWidth);
        }
    });
}

function initializeImages() {
    const images = document.querySelectorAll(".dish-card img");
    images.forEach((img) => {
        img.classList.add("loading");
        img.onload = function () {
            this.classList.remove("loading");
        };
        img.onerror = function () {
            this.src = "/images/default-food.jpg";
            this.classList.remove("loading");
        };
    });
}

function initializeAddToCartButtons() {
    document.querySelectorAll(".add-to-cart-btn").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const itemId = this.dataset.itemId;
            handleAddToCart(e, itemId);
        });
    });
}

function handleAddToCart(event, itemId) {
    event.preventDefault();
    const button = event.currentTarget;

    // Kiểm tra nếu button đang disabled thì return
    if (button.disabled) {
        return;
    }

    const originalContent = button.innerHTML;

    // Disable button và hiển thị loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    // Kiểm tra CSRF token
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;
    if (!csrfToken) {
        showNotification("CSRF token không tồn tại", "error");
        button.disabled = false;
        button.innerHTML = originalContent;
        return;
    }

    // Gọi API thêm vào giỏ hàng
    fetch("/cart/add", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
        },
        body: JSON.stringify({
            item_id: itemId,
            quantity: 1,
        }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Vui lòng đăng nhập trước!");
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                // Hiển thị icon success
                button.innerHTML = '<i class="fas fa-check"></i>';

                // Cập nhật số lượng trong giỏ hàng
                updateCartCount(data.cartCount);

                // Hiển thị thông báo thành công
                showNotification("Đã thêm món ăn vào giỏ hàng!", "success");
            } else {
                throw new Error(data.message || "Có lỗi xảy ra");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showNotification(
                error.message || "Có lỗi xảy ra khi thêm vào giỏ hàng",
                "error"
            );
            button.innerHTML = originalContent;
        })
        .finally(() => {
            // Khôi phục trạng thái button sau 1.5s
            setTimeout(() => {
                button.innerHTML = originalContent;
                button.disabled = false;
            }, 1500);
        });
}

// Hàm cập nhật số lượng giỏ hàng
function updateCartCount(count) {
    const cartCountElement = document.querySelector(".cart-count");
    if (cartCountElement) {
        cartCountElement.textContent = count;
        cartCountElement.classList.remove("hidden");
    }
}

// Hàm hiển thị thông báo
function showNotification(message, type = "success") {
    // Xóa thông báo cũ nếu có
    const existingNotification = document.querySelector(".notification");
    if (existingNotification) {
        existingNotification.remove();
    }

    // Tạo thông báo mới
    const notification = document.createElement("div");
    notification.className = `notification fixed bottom-4 right-4 p-4 rounded-lg shadow-lg transform transition-all duration-500 z-50
        ${type === "success" ? "bg-green-500" : "bg-red-500"} text-white`;

    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === "success" ? "fa-check-circle" : "fa-exclamation-circle"
            } mr-2"></i>
            <span>${message}</span>
        </div>
    `;

    // Thêm vào body
    document.body.appendChild(notification);

    // Animation hiển thị
    requestAnimationFrame(() => {
        notification.style.transform = "translateY(0)";
        notification.style.opacity = "1";
    });

    // Tự động ẩn sau 3s
    setTimeout(() => {
        notification.style.transform = "translateY(100%)";
        notification.style.opacity = "0";
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 3000);
}

// Thêm hàm changeCategory
function changeCategory(category) {
    // Xóa class active từ tất cả các tab
    document.querySelectorAll(".category-tab").forEach((tab) => {
        tab.classList.remove("active");
    });

    // Thêm class active vào tab được chọn
    const selectedTab = document.querySelector(`[data-category="${category}"]`);
    if (selectedTab) {
        selectedTab.classList.add("active");
    }

    // Ẩn tất cả các content
    document.querySelectorAll(".category-content").forEach((content) => {
        content.classList.add("hidden");
    });

    // Hiển thị content của category được chọn
    const selectedContent = document.querySelector(
        `.category-content[data-category="${category}"]`
    );
    if (selectedContent) {
        selectedContent.classList.remove("hidden");
    }
}
