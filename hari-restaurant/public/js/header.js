document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll(".nav-link");
    const cartIcon = document.getElementById("cart-icon");
    const cartPanel = document.getElementById("cart-panel");
    const closeCart = document.getElementById("close-cart");
    const cartCountElement = document.getElementById("cart-count");
    const cartOverlay = document.getElementById("cart-overlay");

    const currentPath = window.location.pathname.replace(/^\/|\/$/g, "");

    // Thiết lập link active
    navLinks.forEach((link) => {
        const linkPath = link.getAttribute("href").replace(/^\/|\/$/g, "");
        if (linkPath === currentPath) {
            link.classList.add("active");
        }

        link.addEventListener("click", function () {
            navLinks.forEach((nav) => nav.classList.remove("active"));
            this.classList.add("active");
        });
    });

    // Hiển thị hoặc ẩn cart panel
    if (cartIcon && cartPanel && cartOverlay) {
        cartIcon.addEventListener("click", function (e) {
            e.preventDefault();
            toggleCartPanel(true);
            loadCartItems();
        });

        closeCart.addEventListener("click", function () {
            toggleCartPanel(false);
        });

        document.addEventListener("click", function (e) {
            if (!cartPanel.contains(e.target) && !cartIcon.contains(e.target)) {
                toggleCartPanel(false);
            }
        });

        function toggleCartPanel(show) {
            if (show) {
                cartPanel.classList.remove("translate-x-full");
                cartOverlay.classList.remove(
                    "opacity-0",
                    "pointer-events-none"
                );
                cartOverlay.classList.add("opacity-50");
                document.body.style.overflow = "hidden";
            } else {
                cartPanel.classList.add("translate-x-full");
                cartOverlay.classList.remove("opacity-50");
                cartOverlay.classList.add("opacity-0", "pointer-events-none");
                document.body.style.overflow = "";
            }
        }
    }

    // Hàm cập nhật số lượng giỏ hàng
    function updateCartCount() {
        if (!cartCountElement) return;

        fetch("/cart/count", {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        })
            .then((response) => {
                if (!response.ok) throw new Error("Failed to fetch cart count");
                return response.json();
            })
            .then((data) => {
                const count = data.count || 0;
                cartCountElement.textContent = count;
                cartCountElement.classList.toggle("hidden", count === 0);
            })
            .catch((error) =>
                console.error("Error updating cart count:", error)
            );
    }

    // Hàm tải nội dung giỏ hàng
    function loadCartItems() {
        const cartItemsContainer = document.getElementById("cart-items");
        if (!cartItemsContainer) return;

        fetch("/cart/items")
            .then((response) => {
                if (!response.ok) throw new Error("Failed to fetch cart items");
                return response.json();
            })
            .then((data) => {
                if (data.items && data.items.length === 0) {
                    cartItemsContainer.innerHTML = `
                        <div class="empty-cart">
                            <i class="fas fa-shopping-basket text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">Giỏ hàng trống</p>
                            <p class="text-sm text-gray-400">Hãy thêm món ăn vào giỏ hàng của bạn</p>
                        </div>
                    `;
                } else {
                    cartItemsContainer.innerHTML = data.html || "";
                }
                document.getElementById("cart-total").textContent =
                    data.total || "0";
                setupCartEvents();
            })
            .catch((error) =>
                console.error("Error loading cart items:", error)
            );
    }

    // Thiết lập sự kiện trong giỏ hàng
    function setupCartEvents() {
        document.querySelectorAll(".update-quantity").forEach((button) => {
            button.addEventListener("click", function () {
                const action = this.dataset.action;
                const cartId = this.dataset.id;
                const input = document.querySelector(
                    `.quantity-input[data-id="${cartId}"]`
                );
                if (!input) return;

                let quantity = parseInt(input.value);
                quantity =
                    action === "increase"
                        ? quantity + 1
                        : Math.max(quantity - 1, 1);
                input.value = quantity;

                updateCartQuantity(cartId, quantity);
            });
        });

        document.querySelectorAll(".quantity-input").forEach((input) => {
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

        document.querySelectorAll(".delete-item").forEach((button) => {
            button.addEventListener("click", function () {
                const cartId = this.dataset.id;
                if (confirm("Bạn có chắc chắn muốn xóa món này?")) {
                    deleteCartItem(cartId);
                }
            });
        });
    }

    // Cập nhật số lượng sản phẩm
    function updateCartQuantity(cartId, quantity) {
        fetch("/cart/update", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify({ cart_id: cartId, quantity }),
        })
            .then((response) => {
                if (!response.ok)
                    throw new Error("Failed to update cart quantity");
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    loadCartItems();
                    updateCartCount();
                }
            })
            .catch((error) =>
                console.error("Error updating cart quantity:", error)
            );
    }

    // Xóa sản phẩm khỏi giỏ hàng
    function deleteCartItem(cartId) {
        fetch(`/cart/destroy/${cartId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        })
            .then((response) => {
                if (!response.ok) throw new Error("Failed to delete cart item");
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    loadCartItems();
                    updateCartCount();
                }
            })
            .catch((error) =>
                console.error("Error deleting cart item:", error)
            );
    }

    // Cập nhật giỏ hàng lần đầu và định kỳ
    if (cartIcon) {
        updateCartCount();
        setInterval(updateCartCount, 30000); // Cập nhật mỗi 30 giây
    }
});
