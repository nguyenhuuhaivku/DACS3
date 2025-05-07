<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Order header -->
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Order') }} #{{ $order->order_number }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Placed on') }} {{ $order->order_date->format('F j, Y g:i A') }}</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                    ($order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                        ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) 
                                }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($order->type) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Customer Information -->
                        <div class="col-span-1">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('Customer Information') }}</h4>
                            <div class="border rounded-lg overflow-hidden">
                                <div class="px-4 py-3 bg-gray-50 border-b">
                                    <p class="font-medium">{{ $order->user->name }}</p>
                                </div>
                                <div class="px-4 py-3">
                                    <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
                                    @if (isset($order->user->phone))
                                        <p class="text-sm text-gray-600">{{ $order->user->phone }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Order Details -->
                        <div class="col-span-1">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('Order Details') }}</h4>
                            <div class="border rounded-lg overflow-hidden">
                                <div class="px-4 py-3 bg-gray-50 border-b">
                                    <p class="font-medium">{{ __('Type') }}: {{ ucfirst($order->type) }}</p>
                                </div>
                                <div class="px-4 py-3">
                                    <p class="text-sm text-gray-600">
                                        {{ __('Order Date') }}: {{ $order->order_date->format('F j, Y g:i A') }}
                                    </p>
                                    @if($order->type === 'takeaway' && $order->pickup_time)
                                        <p class="text-sm text-gray-600">
                                            {{ __('Pickup Time') }}: {{ $order->pickup_time->format('F j, Y g:i A') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="col-span-1">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('Payment Information') }}</h4>
                            <div class="border rounded-lg overflow-hidden">
                                <div class="px-4 py-3 bg-gray-50 border-b">
                                    <p class="font-medium">{{ __('Total') }}: ${{ number_format($order->total_amount, 2) }}</p>
                                </div>
                                <div class="px-4 py-3">
                                    <p class="text-sm text-gray-600">
                                        {{ __('Subtotal') }}: ${{ number_format($order->total_amount - $order->tax_amount, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ __('Tax') }}: ${{ number_format($order->tax_amount, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($order->special_instructions)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('Special Instructions') }}</h4>
                            <div class="border rounded-lg p-4 bg-gray-50">
                                {{ $order->special_instructions }}
                            </div>
                        </div>
                    @endif

                    <!-- Order Items -->
                    <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('Order Items') }}</h4>
                    <div class="border rounded-lg overflow-hidden mb-6">
                        <div class="bg-gray-50 px-6 py-3 border-b">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-6">{{ __('Item') }}</div>
                                <div class="col-span-2 text-right">{{ __('Price') }}</div>
                                <div class="col-span-2 text-right">{{ __('Quantity') }}</div>
                                <div class="col-span-2 text-right">{{ __('Total') }}</div>
                            </div>
                        </div>

                        <div class="divide-y">
                            @foreach($order->items as $item)
                                <div class="px-6 py-4">
                                    <div class="grid grid-cols-12 gap-4 items-center">
                                        <div class="col-span-6">
                                            <div class="flex items-center">
                                                <img src="{{ $item->menu->image ?? asset('images/food-placeholder.jpg') }}" 
                                                    alt="{{ $item->menu->name }}" class="w-12 h-12 object-cover rounded mr-4">
                                                <div>
                                                    <h5 class="font-medium">{{ $item->menu->name }}</h5>
                                                    @if($item->special_instructions)
                                                        <p class="text-xs text-gray-500 mt-1">{{ $item->special_instructions }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-span-2 text-right">
                                            ${{ number_format($item->price, 2) }}
                                        </div>
                                        <div class="col-span-2 text-right">
                                            {{ $item->quantity }}
                                        </div>
                                        <div class="col-span-2 text-right font-medium">
                                            ${{ number_format($item->subtotal, 2) }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="flex justify-end">
                        <div class="w-full md:w-1/3">
                            <div class="border rounded-lg overflow-hidden">
                                <div class="bg-gray-50 px-6 py-3 border-b font-medium">
                                    {{ __('Order Summary') }}
                                </div>
                                <div class="p-6">
                                    <div class="flex justify-between py-2">
                                        <span>{{ __('Subtotal') }}</span>
                                        <span>${{ number_format($order->total_amount - $order->tax_amount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span>{{ __('Tax') }}</span>
                                        <span>${{ number_format($order->tax_amount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between py-2 font-bold text-lg border-t mt-2 pt-2">
                                        <span>{{ __('Total') }}</span>
                                        <span>${{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex justify-between">
                        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Back') }}
                        </a>

                        @if ($order->status === 'pending')
                            <form action="{{ route('orders.cancel', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                                    onclick="return confirm('{{ __('Are you sure you want to cancel this order?') }}')">
                                    {{ __('Cancel Order') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 