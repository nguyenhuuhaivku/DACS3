@if ($cartItems->isEmpty())
    <div class="text-center py-8">
        <i class="fas fa-shopping-cart text-gray-400 text-5xl mb-4"></i>
        <p class="text-gray-500">Thực đơn của bạn đang trống</p>
        <a href="{{ route('menu') }}" class="text-black hover:underline mt-4 inline-flex items-center gap-2">
            <i class="fas fa-utensils"></i>
            Xem thực đơn
        </a>
    </div>
@else
    @foreach ($cartItems as $item)
        <div class="cart-item flex items-center gap-4 mb-4 p-3 border-b hover:bg-gray-50 transition-colors"
            data-id="{{ $item->id }}">
            <img src="{{ $item->menuItem->ImageURL }}" alt="{{ $item->menuItem->ItemName }}"
                class="w-20 h-20 object-cover rounded-lg shadow-sm">

            <div class="flex-1 text-black">
                <h3 class="font-medium flex items-center gap-2">
                    <i class="fas fa-utensils text-gray-400 text-sm"></i>
                    {{ $item->menuItem->ItemName }}
                </h3>
                <p class="item-price text-gray-600 flex items-center gap-1">
                    <i class="fas fa-tag text-gray-400 text-sm"></i>
                    {{ number_format($item->menuItem->Price, 0) }}₫
                </p>

                <div class="flex text-black items-center gap-2 mt-2">
                    <button class="update-quantity w-8 h-8 rounded-full border hover:bg-gray-100 transition-colors"
                        data-action="decrease" data-id="{{ $item->id }}">
                        <i class="fas fa-minus text-sm"></i>
                    </button>
                    <input type="number" min="1" value="{{ $item->quantity }}"
                        class="quantity-input w-12 text-center border rounded bg-gray-50" data-id="{{ $item->id }}">
                    <button class="update-quantity w-8 h-8 rounded-full border hover:bg-gray-100 transition-colors"
                        data-action="increase" data-id="{{ $item->id }}">
                        <i class="fas fa-plus text-sm"></i>
                    </button>
                </div>
            </div>


            <div class="flex flex-col items-end">
                <div class="item-total font-medium text-gray-600 flex items-center gap-1">
                    <i class="fas fa-coins text-gray-400 text-sm"></i>
                    {{ number_format($item->menuItem->Price * $item->quantity, 0) }}₫
                </div>
                <button class="delete-item text-red-500 hover:text-red-700 mt-2 p-1" data-id="{{ $item->id }}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
    @endforeach
@endif
