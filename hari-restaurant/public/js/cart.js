document.addEventListener("DOMContentLoaded", function () {
    // Xử lý tăng/giảm số lượng sản phẩm
    const updateButtons = document.querySelectorAll(".update-quantity");
    updateButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const action = this.dataset.action;
            const cartId = this.dataset.id;
            const input = document.querySelector(
                `.quantity-input[data-id="${cartId}"]`
            );
            let quantity = parseInt(input.value);

            if (action === "increase") {
                quantity++;
            } else if (action === "decrease" && quantity > 1) {
                quantity--;
            }

            input.value = quantity;
            updateCartQuantity(cartId, quantity);
        });
    });

    // Xử lý nhập trực tiếp số lượng
    const inputs = document.querySelectorAll(".quantity-input");
    inputs.forEach((input) => {
        input.addEventListener("change", function () {
            const cartId = this.dataset.id;
            let quantity = parseInt(this.value);

            if (quantity < 1) {
                quantity = 1;
                this.value = quantity;
            }

            updateCartQuantity(cartId, quantity);
        });
    });

    // Hàm cập nhật số lượng và tính toán giá
    function updateCartQuantity(cartId, quantity) {
        fetch(`/cart/update`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify({
                cart_id: cartId,
                quantity: quantity,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const cartItem = document.querySelector(
                        `.cart-item[data-id="${cartId}"]`
                    );
                    const priceElement = cartItem.querySelector(".item-price");
                    const price = parseFloat(
                        priceElement.textContent.replace(/[^\d]/g, "")
                    );

                    // Cập nhật tổng giá của món
                    const itemTotal = cartItem.querySelector(".item-total");
                    const total = price * quantity;
                    itemTotal.textContent = formatCurrency(total);

                    // Cập nhật tổng giá giỏ hàng
                    updateCartTotal();
                }
            })
            .catch((error) => console.error("Error:", error));
    }

    // Hàm cập nhật tổng giá giỏ hàng
    function updateCartTotal() {
        const cartItems = document.querySelectorAll(".cart-item");
        let total = 0;

        cartItems.forEach((item) => {
            const price = parseInt(
                item
                    .querySelector(".item-price")
                    .textContent.replace(/[^\d]/g, "")
            );
            const quantity = parseInt(
                item.querySelector(".quantity-input").value
            );
            total += price * quantity;
        });

        const totalElement = document.querySelector(".total-amount");
        if (totalElement) {
            totalElement.textContent = formatCurrency(total);
        }
    }

    // Hàm format tiền tệ
    function formatCurrency(amount) {
        return (
            new Intl.NumberFormat("vi-VN", {
                style: "currency",
                currency: "VND",
            })
                .format(amount)
                .replace("₫", "") + "₫"
        );
    }

    // Xử lý xóa sản phẩm
    document.addEventListener("click", function (event) {
        if (
            event.target.classList.contains("delete-item") ||
            event.target.closest(".delete-item")
        ) {
            const cartItem = event.target.closest(".cart-item");
            const cartId = cartItem.dataset.id;

            if (confirm("Bạn có chắc chắn muốn xóa món này?")) {
                fetch(`/cart/destroy/${cartId}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                    },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            cartItem.remove();
                            updateCartTotal();

                            // Kiểm tra nếu giỏ hàng trống
                            const remainingItems =
                                document.querySelectorAll(".cart-item");
                            if (remainingItems.length === 0) {
                                location.reload(); // Tải lại trang để hiển thị giỏ hàng trống
                            }
                        }
                    })
                    .catch((error) => console.error("Error:", error));
            }
        }
    });

    // Thêm vào DOMContentLoaded
    document.querySelectorAll(".item-image").forEach((img) => {
        img.onerror = function () {
            handleImageError(this);
        };
    });

    // Thêm event listener cho các nút trong quantity control
    document
        .querySelectorAll(".quantity-control .qty-btn")
        .forEach((button) => {
            button.addEventListener("click", function () {
                const action = this.dataset.action;
                const cartId = this.dataset.id;
                const input =
                    this.parentElement.querySelector(".quantity-input");
                let quantity = parseInt(input.value);

                if (action === "increase") {
                    quantity++;
                } else if (action === "decrease" && quantity > 1) {
                    quantity--;
                }

                input.value = quantity;
                updateCartQuantity(cartId, quantity);
            });
        });

    // Thêm các biến mới
    const selectAllCheckbox = document.getElementById("select-all");
    const deleteSelectedBtn = document.getElementById("delete-selected");
    const itemCheckboxes = document.querySelectorAll(".item-checkbox");

    // Xử lý chọn tất cả
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            const isChecked = this.checked;
            itemCheckboxes.forEach((checkbox) => {
                checkbox.checked = isChecked;
            });
            updateDeleteButtonVisibility();
        });
    }

    // Xử lý checkbox từng item
    itemCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", updateDeleteButtonVisibility);
    });

    // Hiển thị/ẩn nút xóa đã chọn
    function updateDeleteButtonVisibility() {
        const checkedItems = document.querySelectorAll(
            ".item-checkbox:checked"
        );
        if (deleteSelectedBtn) {
            deleteSelectedBtn.classList.toggle(
                "hidden",
                checkedItems.length === 0
            );
        }
    }

    // Xử lý xóa nhiều sản phẩm
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener("click", async function () {
            if (confirm("Bạn có chắc chắn muốn xóa các sản phẩm đã chọn?")) {
                const checkedItems = document.querySelectorAll(
                    ".item-checkbox:checked"
                );
                const itemIds = Array.from(checkedItems).map(
                    (checkbox) => checkbox.dataset.id
                );

                try {
                    const response = await fetch("/cart/destroy-multiple", {
                        method: "DELETE",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                        body: JSON.stringify({ ids: itemIds }),
                    });

                    const data = await response.json();
                    if (data.success) {
                        // Xóa các items khỏi DOM
                        itemIds.forEach((id) => {
                            const item = document.querySelector(
                                `.cart-item[data-id="${id}"]`
                            );
                            if (item) {
                                item.remove();
                            }
                        });

                        // Hiển thị notification
                        showNotification("Đã xóa sản phẩm thành công");

                        // Cập nhật tổng giá
                        updateCartTotal();

                        // Kiểm tra giỏ hàng trống
                        const remainingItems =
                            document.querySelectorAll(".cart-item");
                        if (remainingItems.length === 0) {
                            location.reload();
                        }
                    }
                } catch (error) {
                    console.error("Error:", error);
                }
            }
        });
    }

    // Thêm hàm hiển thị notification
    function showNotification(message) {
        const notification = document.getElementById("cart-notification");

        // Hiển thị notification
        notification.classList.remove("hidden");
        notification.classList.add("slide-in");

        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            notification.classList.remove("slide-in");
            notification.classList.add("slide-out");

            setTimeout(() => {
                notification.classList.add("hidden");
                notification.classList.remove("slide-out");
            }, 300);
        }, 3000);
    }
});

// Thêm vào đầu file
function handleImageError(img) {
    img.onerror = null; // Prevent infinite loop
    img.src = "/images/default-food.jpg";
}
