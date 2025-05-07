<section class="featured-menu py-20 relative overflow-hidden bg-gray-900">
    <div class="container mx-auto px-4">
        <!-- Tiêu đề Section -->
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-playfair text-orange-500 font-bold mb-4">
                <i class="fas fa-utensils text-2xl mr-3"></i>
                Món Ngon Đặc Sắc
                <i class="fas fa-utensils text-2xl ml-3"></i>
            </h2>
            <div class="flex items-center justify-center gap-4">
                <span class="h-[2px] w-20 bg-gradient-to-r from-orange-500 to-transparent"></span>
                <span class="text-gray-400 italic font-playfair text-xl">Signature Dishes</span>
                <span class="h-[2px] w-20 bg-gradient-to-l from-orange-500 to-transparent"></span>
            </div>
        </div>
        <!-- Tabs Danh mục -->
        <div class="mb-12 flex justify-center flex-wrap gap-4" data-aos="fade-up">
            @foreach ($featuredItems as $category => $items)
                <button
                    class="category-tab px-8 py-4 rounded-xl transition-all duration-300 {{ $loop->first ? 'active' : '' }}"
                    data-category="{{ $category }}">
                    @switch($category)
                        @case('Đặc biệt')
                            <i class="fas fa-crown text-yellow-400 mr-2 text-xl"></i>
                        @break

                        @case('Món mới')
                            <i class="fas fa-certificate text-green-400 mr-2 text-xl"></i>
                        @break

                        @case('Phổ biến')
                            <i class="fas fa-fire text-red-400 mr-2 text-xl"></i>
                        @break
                    @endswitch
                    <span class="text-lg font-medium">{{ $category }}</span>
                </button>
            @endforeach
        </div>
        <!-- Lưới món ăn -->
        @foreach ($featuredItems as $category => $items)
            <div class="category-content {{ $loop->first ? 'block' : 'hidden' }}" data-category="{{ $category }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach ($items as $item)
                        <div class="dish-card group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <!-- Hình ảnh và overlay -->
                            <div class="dish-image-container relative overflow-hidden rounded-t-2xl">
                                <div class="dish-image relative h-64">
                                    <img src="{{ asset($item->ImageURL) }}" alt="{{ $item->ItemName }}"
                                        class="w-full h-full object-cover transition-transform duration-700"
                                        onerror="this.src='/images/default-food.jpg'">
                                </div>
                                <div
                                    class="overlay absolute inset-0  opacity-0 group-hover:opacity-100
                                    transition-opacity duration-300 z-10">
                                </div>
                            </div>
                            <!-- Badge trạng thái -->
                            <div class="absolute top-4 right-4 z-20">
                                @switch($item->status)
                                    @case('Đặc biệt')
                                        <span class="status-badge special">
                                            <i class="fas fa-crown"></i>
                                            <span>Đặc biệt</span>
                                        </span>
                                    @break

                                    @case('Món mới')
                                        <span class="status-badge new">
                                            <i class="fas fa-certificate"></i>
                                            <span>Món mới</span>
                                        </span>
                                    @break

                                    @case('Phổ biến')
                                        <span class="status-badge popular">
                                            <i class="fas fa-fire"></i>
                                            <span>Phổ biến</span>
                                        </span>
                                    @break
                                @endswitch
                            </div>
                            <!-- Nội dung -->
                            <div class="dish-content relative z-20">
                                <h3 class="dish-name">{{ $item->ItemName }}</h3>
                                @if ($item->Description)
                                    <p class="dish-description">{{ $item->Description }}</p>
                                @endif
                                <div class="dish-footer">
                                    <div class="dish-price">
                                        {{ number_format($item->Price, 0, ',', '.') }}₫
                                    </div>
                                    <button type="button" onclick="handleAddToCart(event, {{ $item->ItemID }})"
                                        class="add-to-cart-btn z-20" data-item-id="{{ $item->ItemID }}">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <!-- Thông báo -->
    <div id="notification"
        class="fixed bottom-4 right-4 transform translate-y-full opacity-0 transition-all duration-300">
    </div>
</section>
<link rel="stylesheet" href="{{ secure_asset('css/featured-dishes.css') }}">
<script src="{{ secure_asset('js/featured-dishes.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/featured-dishes.css') }}">
<script src="{{ asset('js/featured-dishes.js') }}"></script>
