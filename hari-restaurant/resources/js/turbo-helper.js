import * as Turbo from "@hotwired/turbo";

// Cấu hình Turbo
Turbo.setProgressBarDelay(100);

// Xử lý animation khi chuyển trang
document.addEventListener("turbo:before-render", (event) => {
    // Thêm animation fade out
    document.body.classList.add("page-leave");
});

document.addEventListener("turbo:render", () => {
    // Thêm animation fade in
    document.body.classList.remove("page-leave");
    document.body.classList.add("page-enter");

    // Khởi tạo lại các script cần thiết
    if (window.AOS) {
        window.AOS.refresh();
    }
});

document.addEventListener("turbo:load", () => {
    // Xử lý sau khi trang đã load xong
    setTimeout(() => {
        document.body.classList.remove("page-enter");
    }, 300);
});
