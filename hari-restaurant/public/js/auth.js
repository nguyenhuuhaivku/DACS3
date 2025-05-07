document.addEventListener("DOMContentLoaded", function () {
    const signInButton = document.getElementById("signIn");
    const signUpButton = document.getElementById("signUp");
    const container = document.querySelector(".container");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");
    const passwordError = document.getElementById("password-error");
    const registerButton = document.getElementById("register-button");
    const alerts = document.querySelectorAll(".alert");

    signInButton.addEventListener("click", () => {
        container.classList.remove("right-panel-active");
    });

    signUpButton.addEventListener("click", () => {
        container.classList.add("right-panel-active");
    });
    // Kiểm tra khi người dùng nhập vào trường xác nhận mật khẩu
    confirmPassword.addEventListener("input", function () {
        if (password.value !== confirmPassword.value) {
            passwordError.style.display = "block";
            registerButton.disabled = true; // Vô hiệu hóa nút đăng ký nếu không khớp
        } else {
            passwordError.style.display = "none";
            registerButton.disabled = false; // Kích hoạt nút đăng ký nếu khớp
        }
    });
    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.transition = "opacity 0.5s";
            alert.style.opacity = 0;
            setTimeout(() => alert.remove(), 500); // Xóa phần tử sau khi ẩn
        }, 3000); // 3 giây
    });
});
