<section class="about-section py-16 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <!-- Phần hình ảnh -->
            <div class="grid grid-cols-2 gap-4" data-aos="fade-right">
                <div class="space-y-4">
                    <img src="{{ asset('images\home\anh_1.jpg') }}" alt="Restaurant Interior"
                        class="w-full h-64 object-cover rounded-lg shadow-lg" data-aos="zoom-in" data-aos-delay="200">
                    <img src="{{ asset('images\home\anh_2.jpg') }}" alt="Signature Dish"
                        class="w-full h-48 object-cover rounded-lg shadow-lg" data-aos="zoom-in" data-aos-delay="300">
                </div>
                <div class="space-y-4 mt-8">
                    <img src="{{ asset('images\home\anh_3.jpg') }}" alt="Restaurant Ambiance"
                        class="w-full h-48 object-cover rounded-lg shadow-lg" data-aos="zoom-in" data-aos-delay="400">
                    <img src="{{ asset('images\home\anh_4.jpg') }}" alt="Special Dish"
                        class="w-full h-64 object-cover rounded-lg shadow-lg" data-aos="zoom-in" data-aos-delay="500">
                </div>
            </div>

            <!-- Phần nội dung -->
            <div class="text-left" data-aos="fade-left" data-aos-delay="200">
                <h2 class="text-orange-400 text-xl mb-2">Về Chúng Tôi</h2>
                <h3 class="text-4xl font-playfair text-white mb-6">Chào mừng đến với Hari Restaurant</h3>
                <p class="text-gray-300 mb-6" data-aos="fade-up" data-aos-delay="300">
                    Với hơn 15 năm kinh nghiệm trong ngành ẩm thực, Hari Restaurant tự hào mang đến cho quý khách những
                    trải nghiệm ẩm thực độc đáo và tinh tế nhất.
                </p>
                <p class="text-gray-300 mb-8" data-aos="fade-up" data-aos-delay="400">
                    Chúng tôi cam kết sử dụng những nguyên liệu tươi ngon nhất, kết hợp với bí quyết nấu ăn truyền thống
                    để tạo nên những món ăn đặc sắc.
                </p>

                <!-- Số liệu thống kê -->
                <div class="grid grid-cols-2 gap-8">
                    <div class="text-center counter-box" data-aos="fade-up" data-aos-delay="500">
                        <span class="text-5xl font-bold text-orange-400 counter" data-count="15">0</span>
                        <div class="text-white mt-2">
                            <p class="font-semibold">Năm Kinh Nghiệm</p>
                            <p class="text-sm text-gray-400">EXPERIENCE</p>
                        </div>
                    </div>
                    <div class="text-center counter-box" data-aos="fade-up" data-aos-delay="600">
                        <span class="text-5xl font-bold text-orange-400 counter" data-count="50">0</span>
                        <div class="text-white mt-2">
                            <p class="font-semibold">Đầu Bếp Chuyên Nghiệp</p>
                            <p class="text-sm text-gray-400"><i class="fa-solid fa-hat-chef"></i>MASTER CHEFS</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Phần Features -->
<section class="features py-16 bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="bg-gray-800 p-8 rounded-lg text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="text-orange-400 text-4xl mb-4">
                    <i class="fa-solid fa-hat-wizard"></i>
                </div>
                <h3 class="text-white text-xl font-semibold mb-3">Đầu Bếp Chuyên Nghiệp</h3>
                <p class="text-gray-400">Đội ngũ đầu bếp giàu kinh nghiệm, được đào tạo chuyên sâu</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-gray-800 p-8 rounded-lg text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="text-orange-400 text-4xl mb-4">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3 class="text-white text-xl font-semibold mb-3">Món Ăn Chất Lượng</h3>
                <p class="text-gray-400">Nguyên liệu tươi ngon, chế biến theo tiêu chuẩn cao cấp</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-gray-800 p-8 rounded-lg text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="text-orange-400 text-4xl mb-4">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="text-white text-xl font-semibold mb-3">Đặt Hàng Online</h3>
                <p class="text-gray-400">Dễ dàng đặt món và thanh toán trực tuyến</p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-gray-800 p-8 rounded-lg text-center" data-aos="fade-up" data-aos-delay="400">
                <div class="text-orange-400 text-4xl mb-4">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="text-white text-xl font-semibold mb-3">Hỗ Trợ 24/7</h3>
                <p class="text-gray-400">Luôn sẵn sàng phục vụ và hỗ trợ quý khách</p>
            </div>
        </div>
    </div>
</section>
