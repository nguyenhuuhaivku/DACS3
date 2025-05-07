document.addEventListener("DOMContentLoaded", function () {
    const cartIconCount = document.getElementById("cart-count");
    const successMessage = document.getElementById("cart-success-message");
    const cartButtons = document.querySelectorAll(".menu-item-button");
    const searchInput = document.getElementById("menu-search-input");
    const searchToggle = document.getElementById("searchToggle");
    const searchBox = document.getElementById("searchBox");
    const searchClose = document.getElementById("searchClose");
    const categoryButtons = document.querySelectorAll(".menu-category");
    const categoryItems = document.querySelectorAll(".menu-category-items");
    const searchSuggestions = document.createElement("div");
    searchSuggestions.className = "search-suggestions";
    searchBox.appendChild(searchSuggestions);
    const filterToggle = document.getElementById("filterToggle");
    const filterBox = document.getElementById("filterBox");
    const priceRange = document.getElementById("priceRange");
    const minPrice = document.getElementById("minPrice");
    const maxPrice = document.getElementById("maxPrice");
    const sortBy = document.getElementById("sortBy");
    const availableOnly = document.getElementById("availableOnly");
    const applyFilters = document.getElementById("applyFilters");
    const resetFilters = document.getElementById("resetFilters");

    // Hàm animation cho items trong category
    function animateItems(categoryID) {
        const items = document.querySelectorAll(
            `.menu-category-items[data-category="${categoryID}"] .menu-item`
        );

        items.forEach((item) => {
            item.style.opacity = "0";
            item.style.transform = "translateY(30px)";
        });

        items.forEach((item, index) => {
            setTimeout(() => {
                item.style.transition = "all 1s ease";
                item.style.opacity = "1";
                item.style.transform = "translateY(0)";
            }, index * 100);
        });
    }

    // Hàm hiển thị danh mục
    function showCategory(categoryID) {
        categoryButtons.forEach((btn) => btn.classList.remove("active"));
        categoryItems.forEach((items) => {
            items.classList.remove("active");
            items.style.display = "none";
        });

        const activeButton = document.querySelector(
            `.menu-category[data-category="${categoryID}"]`
        );
        if (activeButton) {
            activeButton.classList.add("active");
        }

        const activeItems = document.querySelector(
            `.menu-category-items[data-category="${categoryID}"]`
        );
        if (activeItems) {
            activeItems.classList.add("active");
            activeItems.style.display = "grid";
            animateItems(categoryID);
        }
    }

    // Xử lý sự kiện click danh mục
    categoryButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const categoryID = this.getAttribute("data-category");
            showCategory(categoryID);
            // Reset search khi chuyển danh mục
            if (searchInput.value.trim() === "") {
                searchSuggestions.style.display = "none";
            }
        });
    });

    // Hiển thị danh mục đầu tiên khi tải trang
    if (categoryButtons.length > 0) {
        const firstCategoryID =
            categoryButtons[0].getAttribute("data-category");
        showCategory(firstCategoryID);
    }

    // Xử lý tìm kiếm và gợi ý
    searchInput.addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase().trim();
        if (searchTerm === "") {
            searchSuggestions.innerHTML = "";
            searchSuggestions.style.display = "none";
            return;
        }

        const menuItems = document.querySelectorAll(".menu-item");
        const suggestions = [];

        menuItems.forEach((item) => {
            const itemName = item.dataset.name.toLowerCase();
            if (itemName.includes(searchTerm)) {
                const categoryElement = item.closest(".menu-category-items");
                const categoryId = categoryElement.dataset.category;
                const categoryButton = document.querySelector(
                    `.menu-category[data-category="${categoryId}"]`
                );
                const categoryName = categoryButton
                    ? categoryButton.textContent.trim()
                    : "";

                suggestions.push({
                    name: item.dataset.name,
                    category: categoryId,
                    categoryName: categoryName,
                });
            }
        });

        if (suggestions.length > 0) {
            searchSuggestions.innerHTML = suggestions
                .map(
                    (suggestion) => `
                <div class="suggestion-item" data-category="${suggestion.category}">
                    <i class="fas fa-utensils"></i>
                    <span>${suggestion.name}</span>
                    <span class="category-label">${suggestion.categoryName}</span>
                </div>
            `
                )
                .join("");
            searchSuggestions.style.display = "block";

            document.querySelectorAll(".suggestion-item").forEach((item) => {
                item.addEventListener("click", function () {
                    const categoryId = this.dataset.category;
                    const itemName =
                        this.querySelector("span").textContent.trim();
                    searchInput.value = itemName;
                    performSearch(itemName, categoryId);
                    searchSuggestions.style.display = "none";
                });
            });
        } else {
            searchSuggestions.innerHTML = `
                <div class="suggestion-item no-result">
                    <i class="fas fa-times-circle"></i>
                    <span>Không tìm thấy món ăn</span>
                </div>
            `;
            searchSuggestions.style.display = "block";
        }
    });

    // Hàm thực hiện tìm kiếm
    function performSearch(searchTerm, categoryId) {
        // Chuyển đến đúng category
        showCategory(categoryId);

        // Tìm và highlight món ăn
        const menuItems = document.querySelectorAll(
            `.menu-category-items[data-category="${categoryId}"] .menu-item`
        );
        menuItems.forEach((item) => {
            item.classList.remove("highlight");
            if (item.dataset.name.toLowerCase() === searchTerm.toLowerCase()) {
                item.classList.add("highlight");
                setTimeout(() => {
                    item.scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                }, 3300);
            }
        });
    }

    // Xử lý đóng/mở search box và các event khác
    searchToggle.addEventListener("click", () => {
        if (
            searchBox.classList.contains("active") &&
            searchInput.value.trim() !== ""
        ) {
            const firstSuggestion = document.querySelector(
                ".suggestion-item:not(.no-result)"
            );
            if (firstSuggestion) {
                const categoryId = firstSuggestion.dataset.category;
                performSearch(searchInput.value.trim(), categoryId);
            }
        } else {
            searchBox.classList.add("active");
            searchInput.focus();
        }
    });

    // Xử lý phím Enter
    searchInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter" && this.value.trim() !== "") {
            const firstSuggestion = document.querySelector(
                ".suggestion-item:not(.no-result)"
            );
            if (firstSuggestion) {
                const categoryId = firstSuggestion.dataset.category;
                performSearch(this.value.trim(), categoryId);
                searchSuggestions.style.display = "none";
            }
        }
    });

    // Reset tìm kiếm
    function resetSearch() {
        searchInput.value = "";
        searchSuggestions.style.display = "none";
        document.querySelectorAll(".menu-item").forEach((item) => {
            item.classList.remove("highlight");
        });
    }

    // Xử lý đóng search box khi click ngoài
    document.addEventListener("click", (e) => {
        if (!searchBox.contains(e.target) && !searchToggle.contains(e.target)) {
            searchBox.classList.remove("active");
            resetSearch();
        }
    });

    searchClose.addEventListener("click", () => {
        searchBox.classList.remove("active");
        resetSearch();
    });

    // Toggle filter box
    filterToggle.addEventListener("click", () => {
        filterBox.classList.toggle("active");
    });

    // Close filter box when clicking outside
    document.addEventListener("click", (e) => {
        if (!filterBox.contains(e.target) && !filterToggle.contains(e.target)) {
            filterBox.classList.remove("active");
        }
    });

    // Update price inputs when range changes
    priceRange.addEventListener("input", (e) => {
        maxPrice.value = e.target.value;
    });

    // Apply filters
    applyFilters.addEventListener("click", () => {
        const filters = {
            minPrice: parseInt(minPrice.value) || 0,
            maxPrice: parseInt(maxPrice.value) || 500000,
            sortBy: sortBy.value,
            availableOnly: availableOnly.checked,
        };

        // Get all menu items
        const menuItems = document.querySelectorAll(".menu-item");

        menuItems.forEach((item) => {
            const price = parseInt(
                item
                    .querySelector(".menu-item-price")
                    .textContent.replace(/[^\d]/g, "")
            );
            const isAvailable = !item.classList.contains("unavailable");

            // Check if item matches filters
            const matchesPrice =
                price >= filters.minPrice && price <= filters.maxPrice;
            const matchesAvailability = !filters.availableOnly || isAvailable;

            // Show/hide items based on filters
            if (matchesPrice && matchesAvailability) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });

        // Sort items
        const itemsContainer = document.querySelector(
            ".menu-category-items.active"
        );
        const items = Array.from(itemsContainer.children);

        items.sort((a, b) => {
            const priceA = parseInt(
                a
                    .querySelector(".menu-item-price")
                    .textContent.replace(/[^\d]/g, "")
            );
            const priceB = parseInt(
                b
                    .querySelector(".menu-item-price")
                    .textContent.replace(/[^\d]/g, "")
            );
            const nameA = a.querySelector("h3").textContent.trim();
            const nameB = b.querySelector("h3").textContent.trim();

            switch (filters.sortBy) {
                case "price-asc":
                    return priceA - priceB;
                case "price-desc":
                    return priceB - priceA;
                case "name-asc":
                    return nameA.localeCompare(nameB);
                case "name-desc":
                    return nameB.localeCompare(nameA);
                default:
                    return 0;
            }
        });

        // Re-append sorted items
        items.forEach((item) => itemsContainer.appendChild(item));

        filterBox.classList.remove("active");
    });

    // Reset filters
    resetFilters.addEventListener("click", () => {
        // Reset filter inputs
        minPrice.value = "";
        maxPrice.value = "500000";
        priceRange.value = 500000;
        sortBy.value = "default";
        availableOnly.checked = false;

        // Reset menu items display and order
        const activeCategory = document.querySelector(".menu-category.active");
        const categoryId = activeCategory.getAttribute("data-category");

        // Show current category items
        document
            .querySelectorAll(".menu-category-items")
            .forEach((container) => {
                if (container.getAttribute("data-category") === categoryId) {
                    container.style.display = "grid";
                    const items = Array.from(container.children);
                    items.forEach((item) => {
                        item.style.display = "block";
                        item.style.opacity = "0";
                        item.style.transform = "translateY(30px)";
                    });

                    items.forEach((item, index) => {
                        setTimeout(() => {
                            item.style.transition = "all 1s ease";
                            item.style.opacity = "1";
                            item.style.transform = "translateY(0)";
                        }, index * 100);
                    });
                } else {
                    container.style.display = "none";
                }
            });

        // Reset items order to original DOM order
        const activeContainer = document.querySelector(
            `.menu-category-items[data-category="${categoryId}"]`
        );
        const originalItems = Array.from(activeContainer.children);
        originalItems.sort((a, b) => {
            return (
                parseInt(a.dataset.originalIndex || 0) -
                parseInt(b.dataset.originalIndex || 0)
            );
        });
        originalItems.forEach((item) => activeContainer.appendChild(item));
    });

    document.querySelectorAll(".menu-category-items").forEach((container) => {
        Array.from(container.children).forEach((item, index) => {
            item.dataset.originalIndex = index;
        });
    });

    function fetchCartCount() {
        fetch("/cart/count", {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    cartIconCount.textContent = data.cartCount;
                } else {
                    console.error("Không thể lấy số lượng giỏ hàng");
                }
            })
            .catch((error) => console.error("Error:", error));
    }

    // Gọi hàm fetchCartCount khi tải trang
    fetchCartCount();

    function showNotification(message) {
        const notification = document.getElementById("cart-success-message");

        // Thêm class để hiển thị notification
        notification.classList.remove("hidden");
        notification.classList.add("slide-in");

        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            notification.classList.remove("slide-in");
            notification.classList.add("slide-out");

            // Ẩn hoàn toàn sau khi animation kết thúc
            setTimeout(() => {
                notification.classList.add("hidden");
                notification.classList.remove("slide-out");
            }, 300);
        }, 3000);
    }

    // Cập nhật phần xử lý thêm vào giỏ hàng
    cartButtons.forEach((button) => {
        button.addEventListener("click", async function () {
            const itemId = button.dataset.id;
            const originalContent = button.innerHTML;

            // Disable button ngay lập tức
            button.disabled = true;

            // Hiệu ứng loading với transition
            button.classList.add("loading");
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            // Đợi animation loading
            await new Promise((resolve) => setTimeout(resolve, 800));

            try {
                const response = await fetch("/cart/add", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                    },
                    body: JSON.stringify({ item_id: itemId, quantity: 1 }),
                });

                const data = await response.json();

                if (data.success) {
                    // Chuyển từ loading sang success
                    button.classList.remove("loading");
                    button.classList.add("processing");
                    button.innerHTML =
                        '<i class="fas fa-circle-notch fa-spin"></i>';

                    // Đợi animation processing
                    await new Promise((resolve) => setTimeout(resolve, 500));

                    // Hiển thị icon check với hiệu ứng
                    button.classList.remove("processing");
                    button.classList.add("success");
                    button.innerHTML = '<i class="fas fa-check"></i>';

                    // Cập nhật số lượng giỏ hàng với animation
                    const cartCount = document.getElementById("cart-count");
                    if (cartCount) {
                        cartCount.classList.add("cart-update");
                        cartCount.textContent = data.cartCount;
                        setTimeout(
                            () => cartCount.classList.remove("cart-update"),
                            300
                        );
                    }

                    // Hiển thị notification sau một chút delay
                    setTimeout(() => {
                        showNotification(data.message);
                    }, 200);
                } else {
                    // Xử lý lỗi với animation
                    button.classList.remove("loading");
                    button.classList.add("error");
                    button.innerHTML = '<i class="fas fa-times"></i>';
                }
            } catch (error) {
                console.error("Error:", error);
                button.classList.remove("loading");
                button.classList.add("error");
                button.innerHTML = '<i class="fas fa-times"></i>';
            }

            // Reset button state với animation mượt mà
            setTimeout(() => {
                button.classList.remove("success", "error", "processing");
                button.classList.add("resetting");

                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                    button.classList.remove("resetting");
                }, 300);
            }, 2000);
        });
    });

    // Validate giá trị filter
    function validatePriceInputs() {
        const min = parseInt(minPrice.value) || 0;
        const max = parseInt(maxPrice.value) || 500000;

        if (min < 0) minPrice.value = 0;
        if (max > 500000) maxPrice.value = 500000;
        if (min > max) {
            minPrice.value = max;
            maxPrice.value = max;
        }
    }

    // Thêm validation cho input giá
    minPrice.addEventListener("change", validatePriceInputs);
    maxPrice.addEventListener("change", validatePriceInputs);

    // Validate search input
    searchInput.addEventListener("input", function (e) {
        const value = e.target.value.trim();

        // Giới hạn độ dài input
        if (value.length > 255) {
            e.target.value = value.substring(0, 255);
            return;
        }

        // Chỉ cho phép chữ cái, số và một số ký tự đặc biệt
        const sanitizedValue = value.replace(
            /[^a-zA-Z0-9\s\-_àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/g,
            ""
        );
        if (sanitizedValue !== value) {
            e.target.value = sanitizedValue;
            return;
        }
        // Chỉ tìm kiếm khi có ít nhất 2 ký tự
        if (value.length < 2) {
            searchSuggestions.style.display = "none";
            return;
        }
    });

    // XSS protection cho nội dung động
    function sanitizeHTML(str) {
        const div = document.createElement("div");
        div.textContent = str;
        return div.innerHTML;
    }

    // hiển thị suggestion
    function updateSuggestions(suggestions) {
        searchSuggestions.innerHTML = suggestions
            .map(
                (suggestion) => `
                <div class="suggestion-item" data-category="${sanitizeHTML(
                    suggestion.category
                )}">
                    <i class="fas fa-utensils"></i>
                    <span>${sanitizeHTML(suggestion.name)}</span>
                    <span class="category-label">${sanitizeHTML(
                        suggestion.categoryName
                    )}</span>
                </div>
            `
            )
            .join("");
    }

    let searchTimeout;
    searchInput.addEventListener("input", function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            //  tìm kiếm
        }, 300);
    });
});
document.addEventListener("DOMContentLoaded", () => {
    // Xử lý thêm vào giỏ hàng chỉ khi món ăn còn
    document.querySelectorAll(".menu-item-button").forEach((button) => {
        if (button.classList.contains("disabled")) {
            button.addEventListener("click", (e) => {
                e.preventDefault();
                alert("Món ăn này đã hết.");
            });
        }
    });
});
