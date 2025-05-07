document.addEventListener("DOMContentLoaded", function () {
    const loader = document.querySelector(".loader-wrapper");
    const loadingText = document.querySelector(".loading-text");
    let dots = 0;

    // Animation cho dấu chấm loading
    const animateDots = setInterval(() => {
        dots = (dots + 1) % 4;
        loadingText.textContent = "ĐANG TẢI" + ".".repeat(dots);
    }, 500);

    // Ẩn loader sau khi tải xong
    window.addEventListener("load", () => {
        setTimeout(() => {
            clearInterval(animateDots);
            loader.style.opacity = "0";
            loader.addEventListener("transitionend", () => {
                loader.style.display = "none";
            });
        }, 1000); // Đợi 2s để hiển thị animation
    });
});
