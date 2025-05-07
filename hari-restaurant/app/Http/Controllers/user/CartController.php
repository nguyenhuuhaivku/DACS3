<?php


namespace App\Http\Controllers\user;


use App\Models\user\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class CartController extends Controller
{
    // Hiển thị giỏ hàng
    public function index()
    {
        $user = Auth::user();
        $cartItems = Cart::with('menuItem')->where('user_id', $user->id)->get();


        return view('user.cart.index', compact('cartItems'));
    }


    // Thêm sản phẩm vào giỏ hàng
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:menuitem,ItemID',
            'quantity' => 'required|integer|min:1',
        ]);


        $cart = Cart::where('user_id', Auth::id())
            ->where('item_id', $request->item_id)
            ->first();


        if ($cart) {
            // Nếu sản phẩm đã tồn tại, tăng số lượng
            $cart->increment('quantity', $request->quantity);
        } else {
            // Nếu sản phẩm chưa tồn tại, thêm mới
            Cart::create([
                'user_id' => Auth::id(),
                'item_id' => $request->item_id,
                'quantity' => $request->quantity,
            ]);
        }


        // Tính tổng số lượng sản phẩm trong giỏ hàng
        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');


        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng thành công!',
            'cartCount' => $cartCount,
        ]);
    }


    // Cập nhật số lượng
    public function update(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:cart,id',
            'quantity' => 'required|integer|min:1',
        ]);


        $cart = Cart::where(
            'id',
            $request->cart_id
        )->where('user_id', Auth::id())->first();


        if ($cart) {
            $cart->update(['quantity' => $request->quantity]);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật giỏ hàng thành công!',
            ]);
        }


        return response()->json([
            'success' => false,
            'message' => 'Không thể cập nhật giỏ hàng!',
        ]);
    }


    // Xóa sản phẩm khỏi giỏ hàng
    public function destroy($id)
    {
        $cart = Cart::where(
            'id',
            $id
        )
            ->where('user_id', Auth::id())
            ->first();


        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại trong giỏ hàng!'
            ], 404);
        }


        try {
            $cart->delete();
            return response()->json([
                'success' => true,
                'message' => 'Sản phẩm đã được xóa thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa sản phẩm không thành công!'
            ], 500);
        }
    }


    public function count()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }


            $count = Cart::where('user_id', Auth::id())->sum('quantity');


            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching cart count'
            ], 500);
        }
    }


    public function summary()
    {
        $cartItems = Cart::with('menuItem')->where('user_id', Auth::id())->get();
        $cartTotal = $cartItems->sum(fn($item) => $item->menuItem->Price * $item->quantity);


        return response()->json([
            'success' => true,
            'cartTotal' => $cartTotal,
        ]);
    }
    public function getCartItems()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->whereNull('ReservationID')
            ->with(['menuItem' => function ($query) {
                $query->select('ItemID', 'ItemName', 'Price', 'ImageURL', 'Description');
            }])
            ->get();


        // Đảm bảo URL hình ảnh đầy đủ và xử lý null
        $cartItems->each(function ($item) {
            if ($item->menuItem) {
                // Kiểm tra xem ImageURL có bắt đầu bằng http không
                if ($item->menuItem->ImageURL && !str_starts_with($item->menuItem->ImageURL, 'http')) {
                    $item->menuItem->ImageURL = asset($item->menuItem->ImageURL);
                }
            }
        });


        $total = number_format($cartItems->sum(function ($item) {
            return $item->menuItem ? $item->menuItem->Price * $item->quantity : 0;
        })) . 'đ';


        $html = view('user.cart.cart-items', compact('cartItems'))->render();


        return response()->json([
            'html' => $html,
            'total' => $total,
            'items' => $cartItems
        ]);
    }
}
