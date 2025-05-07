document.addEventListener("DOMContentLoaded", function () {
    // Hàm format tiền tệ
    function formatCurrency(amount) {
        return new Intl.NumberFormat("vi-VN").format(amount) + " VNĐ";
    }

    // Hàm cập nhật tổng tiền
    function updateTotalAmount() {
        const reservationItems = document.querySelectorAll(".reservation-item");
        let total = 0;

        reservationItems.forEach((item) => {
            const price = parseInt(
                item.querySelector(".reservation-item-price").dataset.price
            );
            const quantity = parseInt(
                item.querySelector(".reservation-quantity-input").value
            );
            total += price * quantity;
        });

        const totalElement = document.querySelector(
            ".reservation-total-amount"
        );
        if (totalElement) {
            totalElement.textContent = formatCurrency(total);
        }
    }

    // Hàm cập nhật số lượng
    function updateCartItem(cartId, quantity) {
        fetch("/cart/update", {
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
                    updateTotalAmount();
                }
            })
            .catch((error) => console.error("Error:", error));
    }

    // Xử lý tăng/giảm số lượng
    document
        .querySelectorAll(".reservation-update-quantity")
        .forEach((button) => {
            button.addEventListener("click", function () {
                const action = this.dataset.action;
                const cartId = this.dataset.id;
                const input = this.closest(
                    ".reservation-quantity-control"
                ).querySelector(".reservation-quantity-input");
                let quantity = parseInt(input.value);

                if (action === "increase") {
                    quantity++;
                } else if (action === "decrease" && quantity > 1) {
                    quantity--;
                }

                input.value = quantity;
                updateCartItem(cartId, quantity);
            });
        });

    // Xử lý nhập trực tiếp số lượng
    document
        .querySelectorAll(".reservation-quantity-input")
        .forEach((input) => {
            input.addEventListener("change", function () {
                const cartId = this.dataset.id;
                let quantity = parseInt(this.value);

                if (quantity < 1) {
                    quantity = 1;
                    this.value = quantity;
                }

                updateCartItem(cartId, quantity);
            });
        });

    // Xử lý xóa món
    document.querySelectorAll(".reservation-delete-item").forEach((button) => {
        button.addEventListener("click", function () {
            const cartId = this.dataset.id;
            const reservationItem = this.closest(".reservation-item");

            if (confirm("Bạn có chắc muốn xóa món này?")) {
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
                            reservationItem.remove();
                            updateTotalAmount();

                            // Kiểm tra nếu không còn món nào
                            const remainingItems =
                                document.querySelectorAll(".reservation-item");
                            if (remainingItems.length === 0) {
                                location.reload();
                            }
                        }
                    })
                    .catch((error) => console.error("Error:", error));
            }
        });
    });

    // Xử lý lỗi hình ảnh
    document.querySelectorAll(".reservation-item-image").forEach((img) => {
        img.onerror = function () {
            this.onerror = null;
            this.src = "/images/default-food.jpg";
        };
    });

    // Thêm validation cho form đặt bàn
    const reservationForm = document.getElementById("reservation-form");
    const dateTimeInput = document.querySelector(
        'input[name="ReservationDate"]'
    );
    const phoneInput = document.querySelector('input[name="Phone"]');
    const guestCountInput = document.querySelector('input[name="GuestCount"]');

    // Set min datetime là thời gian hiện tại
    function setMinDateTime() {
        const now = new Date();
        now.setMinutes(now.getMinutes() + 30); // Thêm 30 phút từ thời điểm hiện tại

        let hours = now.getHours();
        let minutes = now.getMinutes();
        const dayOfWeek = now.getDay(); // 0-6 (Chủ nhật - Thứ 7)

        // Xác định giờ mở cửa và đóng cửa dựa vào ngày trong tuần
        const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
        const openHour = 8; // Giờ mở cửa
        const openMinute = 15; // Phút bắt đầu nhận khách (sau giờ mở cửa 15 phút)
        const closeHour = isWeekend ? 23 : 22; // Giờ đóng cửa
        const lastOrderHour = isWeekend ? 22 : 21; // Giờ cuối nhận đơn (1 tiếng trước đóng cửa)
        const lastOrderMinute = 0; // Phút cuối nhận đơn

        // Nếu thời gian hiện tại < 8:15, set thành 8:15
        if (hours < openHour || (hours === openHour && minutes < openMinute)) {
            hours = openHour;
            minutes = openMinute;
        }

        // Nếu thời gian hiện tại > giờ đóng cửa, set sang ngày hôm sau 8:15
        if (hours >= closeHour) {
            now.setDate(now.getDate() + 1);
            hours = openHour;
            minutes = openMinute;
        }

        now.setHours(hours, minutes);

        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, "0");
        const day = String(now.getDate()).padStart(2, "0");
        const formattedHours = String(hours).padStart(2, "0");
        const formattedMinutes = String(minutes).padStart(2, "0");

        const minDateTime = `${year}-${month}-${day}T${formattedHours}:${formattedMinutes}`;
        dateTimeInput.min = minDateTime;

        // Set max date là 30 ngày từ hiện tại
        const maxDate = new Date();
        maxDate.setDate(maxDate.getDate() + 30);
        const maxYear = maxDate.getFullYear();
        const maxMonth = String(maxDate.getMonth() + 1).padStart(2, "0");
        const maxDay = String(maxDate.getDate()).padStart(2, "0");
        // Set max time dựa vào ngày trong tuần
        const maxDayOfWeek = maxDate.getDay();
        const maxLastOrderHour =
            maxDayOfWeek === 0 || maxDayOfWeek === 6 ? "22:00" : "21:00";
        dateTimeInput.max = `${maxYear}-${maxMonth}-${maxDay}T${maxLastOrderHour}`;
    }

    setMinDateTime();

    // Validate phone number
    phoneInput.addEventListener("input", function (e) {
        // Chỉ cho phép nhập số
        this.value = this.value.replace(/[^\d]/g, "");

        // Giới hạn độ dài số điện thoại
        if (this.value.length > 10) {
            this.value = this.value.slice(0, 10);
        }
    });

    // Validate số lượng khách
    guestCountInput.addEventListener("input", function (e) {
        const value = parseInt(this.value);
        if (value < 1) {
            this.value = 1;
        } else if (value > 20) {
            // Giới hạn tối đa 20 người
            this.value = 20;
        }
    });

    // Xử lý input họ tên
    const fullNameInput = document.querySelector('input[name="FullName"]');

    fullNameInput.addEventListener("input", function (e) {
        let value = this.value;

        // Loại bỏ các ký tự không phải chữ cái và khoảng trắng
        value = value.replace(/[^a-zA-ZÀ-ỹ\s]/g, "");

        // Loại bỏ khoảng trắng liên tiếp
        value = value.replace(/\s+/g, " ");

        // Giới hạn độ dài
        if (value.length > 50) {
            value = value.substring(0, 50);
        }

        // Cập nhật giá trị
        this.value = value;
    });

    fullNameInput.addEventListener("blur", function () {
        // Trim khoảng trắng khi blur
        this.value = this.value.trim();
    });

    // Validate form trước khi submit
    reservationForm.addEventListener("submit", function (e) {
        e.preventDefault(); // Ngăn form submit mặc định
        let isValid = true;
        let firstError = null;

        // Validate họ tên
        const fullName = fullNameInput.value.trim();
        if (fullName.length < 2) {
            showError(fullNameInput, "Họ tên phải có ít nhất 2 ký tự");
            isValid = false;
            firstError = firstError || fullNameInput;
        } else if (!/^[\p{L}\s]+$/u.test(fullName)) {
            showError(
                fullNameInput,
                "Họ tên chỉ được chứa chữ cái và khoảng trắng"
            );
            isValid = false;
            firstError = firstError || fullNameInput;
        } else if (/\s{2,}/.test(fullName)) {
            showError(
                fullNameInput,
                "Tên không được chứa nhiều khoảng trắng liên tiếp"
            );
            isValid = false;
            firstError = firstError || fullNameInput;
        } else {
            removeError(fullNameInput);
        }

        // Validate số điện thoại
        const phone = phoneInput.value.trim();
        if (!phone) {
            showError(phoneInput, "Vui lòng nhập số điện thoại");
            isValid = false;
            firstError = firstError || phoneInput;
        } else if (!/^(0[0-9]{9})$/.test(phone)) {
            showError(phoneInput, "Số điện thoại không hợp lệ");
            isValid = false;
            firstError = firstError || phoneInput;
        } else {
            removeError(phoneInput);
        }

        // Validate số lượng khách
        const guestCount = parseInt(guestCountInput.value);
        if (!guestCount || guestCount < 1) {
            showError(guestCountInput, "Số lượng khách phải lớn hơn 0");
            isValid = false;
            firstError = firstError || guestCountInput;
        } else if (guestCount > 20) {
            showError(guestCountInput, "Số lượng khách không được vượt quá 20");
            isValid = false;
            firstError = firstError || guestCountInput;
        } else {
            removeError(guestCountInput);
        }

        // Validate thời gian đặt bàn
        const reservationDateTime = new Date(dateTimeInput.value);
        const now = new Date();
        now.setMinutes(now.getMinutes() + 30);

        if (!dateTimeInput.value) {
            showError(dateTimeInput, "Vui lòng chọn thời gian đặt bàn");
            isValid = false;
            firstError = firstError || dateTimeInput;
        } else if (reservationDateTime < now) {
            showError(
                dateTimeInput,
                "Thời gian đặt bàn phải sau thời điểm hiện tại ít nhất 30 phút"
            );
            isValid = false;
            firstError = firstError || dateTimeInput;
        } else {
            removeError(dateTimeInput);
        }

        if (isValid) {
            reservationForm.submit(); // Submit form nếu tất cả đều hợp lệ
        } else if (firstError) {
            firstError.focus(); // Focus vào trường lỗi đầu tiên
        }
    });

    // Cập nhật hàm showError và removeError
    function showError(input, message) {
        const formGroup = input.closest(".form-group");
        let errorDiv = formGroup.querySelector(".error-message");

        if (!errorDiv) {
            errorDiv = document.createElement("div");
            errorDiv.className = "error-message";
            formGroup.appendChild(errorDiv);
        }

        input.classList.add("is-invalid");
        errorDiv.textContent = message;
        errorDiv.style.display = "block"; // Hiển thị message

        // Thêm CSS để ngăn zoom trên mobile
        input.style.fontSize = "16px";
        errorDiv.style.fontSize = "14px";
    }

    function removeError(input) {
        const formGroup = input.closest(".form-group");
        const errorDiv = formGroup.querySelector(".error-message");

        input.classList.remove("is-invalid");
        if (errorDiv) {
            errorDiv.style.display = "none";
        }
    }

    function showGlobalError(message) {
        let alertDiv = document.querySelector(".alert-error");

        if (!alertDiv) {
            alertDiv = document.createElement("div");
            alertDiv.className = "alert alert-error";
            reservationForm.insertBefore(alertDiv, reservationForm.firstChild);
        }

        alertDiv.textContent = message;
    }

    // Thêm event listener cho input datetime-local
    dateTimeInput.addEventListener("input", function (e) {
        const selectedDate = new Date(this.value);
        const hours = selectedDate.getHours();
        const minutes = selectedDate.getMinutes();
        const dayOfWeek = selectedDate.getDay();
        const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;

        let errorMessage = "";

        // Kiểm tra giờ mở cửa (8:15)
        if (hours === 8 && minutes < 15) {
            errorMessage =
                "Thời gian đặt bàn phải sau 8:15 (15 phút sau giờ mở cửa)";
        }
        // Kiểm tra giờ đóng cửa theo ngày
        else if (isWeekend) {
            if (hours < 8) {
                errorMessage = "Thời gian đặt bàn cuối tuần phải từ 8:15";
            } else if (hours >= 22) {
                errorMessage = "Thời gian đặt bàn cuối tuần phải trước 22:00 ";
            }
        } else {
            if (hours < 8) {
                errorMessage =
                    "Thời gian đặt bàn các ngày trong tuần phải từ 8:15";
            } else if (hours >= 21) {
                errorMessage =
                    "Thời gian đặt bàn các ngày trong tuần phải trước 21:00 ";
            }
        }

        // Kiểm tra thời gian tối thiểu (30 phút từ hiện tại)
        const now = new Date();
        const minTime = new Date(now.getTime() + 30 * 60000); // Thêm 30 phút
        if (selectedDate < minTime) {
            errorMessage =
                "Thời gian đặt bàn phải sau thời điểm hiện tại ít nhất 30 phút";
        }

        if (errorMessage) {
            showError(this, errorMessage);
        } else {
            removeError(this);
        }
    });

    const style = document.createElement("style");
    style.textContent = `
        .form-group {
            position: relative;
        }


        .error-message {
            position: absolute;
            left: 0;
            right: 0;
            bottom: -10px;
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            line-height: 1.2;
            border-left: 3px solid #e74c3c;
            z-index: 1;
            transform: translateY(100%);
        }


        @media (max-width: 768px) {
            input[type="datetime-local"] {
                font-size: 16px !important;
                -webkit-text-size-adjust: 100%;
            }
           
            .error-message {
                position: static;
                margin-top: 4px;
                transform: none;
                font-size: 12px !important;
                -webkit-text-size-adjust: 100%;
            }
           
            .form-group {
                margin-bottom: 24px;
            }
        }
    `;
    document.head.appendChild(style);
});
