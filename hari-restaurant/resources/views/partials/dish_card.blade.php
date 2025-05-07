<div class="dish-card group" data-aos="fade-up">
    <div class="relative overflow-hidden rounded-2xl bg-white/5 backdrop-blur-sm">
        <!-- hinh anh -->
        <div class="aspect-w-16 aspect-h-12 overflow-hidden">
            <img src="{{ asset($dish->ImageURL) }}" alt="{{ $dish->ItemName }}"
                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
        </div>
        <!-- noi dung -->
        <div
            class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent
                    opacity-0 group-hover:opacity-100 transition-all duration-500">
            <div
                class="absolute bottom-0 left-0 right-0 p-6 transform translate-y-6
                      group-hover:translate-y-0 transition-transform duration-500">
                <h3 class="font-playfair text-2xl text-white mb-2">{{ $dish->ItemName }}</h3>
                @if ($dish->Description)
                <p class="text-gray-300 text-sm mb-4">{{ $dish->Description }}</p>
                @endif
                <div class="flex items-center justify-between">
                    <span class="text-orange-400 font-semibold text-lg">
                        {{ number_format($dish->Price) }}₫
                    </span>
                    @if ($dish->Available)
                    <button onclick="addToCart({{ $dish->ItemID }})"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-full
                                       transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Thêm vào giỏ
                    </button>
                    @else
                    <span class="text-red-500 font-medium">
                        <i class="fas fa-clock mr-2"></i>Tạm hết
                    </span>
                    @endif
                </div>
            </div>
        </div>


        <!-- trang thai -->
        <div class="absolute top-4 right-4">
            <span
                class="px-3 py-1 rounded-full text-sm font-medium
                       {{ $dish->status === 'Đặc biệt'
                           ? 'bg-yellow-400/90 text-yellow-900'
                           : ($dish->status === 'Món mới'
                               ? 'bg-green-400/90 text-green-900'
                               : 'bg-red-400/90 text-red-900') }}">
                @if ($dish->status === 'Đặc biệt')
                <i class="fas fa-crown mr-1"></i>
                @elseif($dish->status === 'Món mới')
                <i class="fas fa-star mr-1"></i>
                @else
                <i class="fas fa-fire mr-1"></i>
                @endif
                {{ $dish->status }}
            </span>
        </div>
    </div>
</div>
<section class="featured-dishes-section py-24 relative overflow-hidden">
    <!-- hinh anh -->
    <div class="absolute inset-0 bg-[url('/images/pattern.png')] opacity-10 animate-slide"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-black/90 via-black/80 to-black/90"></div>


    <div class="container mx-auto px-4 relative z-10">
        <!-- tieu de -->
        <div class="text-center mb-20" data-aos="fade-up">
            <h2 class="text-5xl md:text-6xl font-playfair text-white mb-6">
                Tinh Hoa Ẩm Thực
            </h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Khám phá những món ăn độc đáo và đẳng cấp tại Hari Restaurant
            </p>
        </div>

        <!-- danh muc -->
        <div class="flex justify-center mb-16 space-x-6" data-aos="fade-up" data-aos-delay="200">
            <button class="category-tab active" data-category="Đặc biệt">
                <i class="fas fa-crown text-yellow-400 mr-2"></i>Đặc biệt
            </button>
            <button class="category-tab" data-category="Món mới">
                <i class="fas fa-star text-green-400 mr-2"></i>Món Mới
            </button>
            <button class="category-tab" data-category="Phổ biến">
                <i class="fas fa-fire text-red-400 mr-2"></i>Phổ biến
            </button>
        </div>


        @foreach(['Đặc biệt', 'Món mới', 'Phổ biến'] as $category)
        <div class="category-content {{ $loop->first ? 'active' : 'hidden' }}"
            data-category="{{ $category }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($menuItems->where('status', $category) as $dish)
                <div class="dish-card group" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="relative overflow-hidden rounded-xl bg-white/5 backdrop-blur-sm">
                        <div class="aspect-w-16 aspect-h-12 overflow-hidden">
                            <img src="{{ asset($dish->ImageURL) }}"
                                alt="{{ $dish->ItemName }}"
                                class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110"
                                onerror="this.src='/images/default-food.jpg'">
                        </div>

                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent
                                            opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                <h3 class="text-xl font-semibold text-white mb-2">{{ $dish->ItemName }}</h3>
                                <p class="text-gray-300 text-sm mb-4">{{ $dish->Description }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-orange-400 font-semibold">
                                        {{ number_format($dish->Price) }}₫
                                    </span>
                                    @if($dish->Available)
                                    <button onclick="addToCart({{ $dish->ItemID }})"
                                        class="add-to-cart-btn bg-orange-500 hover:bg-orange-600
                                                               text-white px-4 py-2 rounded-full transform
                                                               hover:scale-105 transition-all duration-300">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        Thêm vào giỏ
                                    </button>
                                    @else
                                    <span class="text-red-500">
                                        <i class="fas fa-clock mr-2"></i>Tạm hết
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 col-span-3 text-center">
                    Không có món ăn nào thuộc danh mục {{ $category }}
                </p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>


    <div id="cart-toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg
                                transform translate-y-full opacity-0 transition-all duration-300">
        <i class="fas fa-check-circle mr-2"></i>
        Đã thêm vào giỏ hàng
    </div>
</section>


<style>
    /* Thêm vào phần style của bạn */
    .category-tab {
        @apply text-white font-medium transition-all duration-300;
    }


    .category-tab.active span {
        @apply bg-gradient-to-r from-orange-600 to-orange-400;
    }


    .status-badge {
        @apply px-3 py-1 rounded-full text-sm font-medium backdrop-blur-sm;
    }


    .status-badge.đặc.biệt {
        @apply bg-yellow-400/80 text-yellow-900;
    }


    .status-badge.món.mới {
        @apply bg-green-400/80 text-green-900;
    }


    .status-badge.phổ.biến {
        @apply bg-red-400/80 text-red-900;
    }


    .add-to-cart-btn {
        @apply relative overflow-hidden px-4 py-2 rounded-full bg-orange-500 text-white transform hover:scale-105 transition-all duration-300 hover:bg-orange-600 hover:shadow-lg hover:shadow-orange-500/30;
    }


    @keyframes slide {
        0% {
            background-position: 0 0;
        }

        100% {
            background-position: 100% 100%;
        }
    }


    .animate-slide {
        animation: slide 20s linear infinite;
    }
</style>


<script>
    function addToCart(itemId) {
        fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    item_id: itemId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const toast = document.getElementById('cart-toast');
                    toast.classList.remove('translate-y-full', 'opacity-0');

                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cartCount;
                        cartCount.classList.remove('hidden');
                    }


                    setTimeout(() => {
                        toast.classList.add('translate-y-full', 'opacity-0');
                    }, 3000);
                }
            });
    }
</script>