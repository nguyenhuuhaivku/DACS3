<?php




namespace App\Http\Controllers\Admin;




use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;




class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.created_at',
            DB::raw('COUNT(DISTINCT reservation.ReservationID) as total_reservations'),
            DB::raw('SUM(payment.Amount) as total_spent'),
            DB::raw('MAX(reservation.CreatedAt) as last_reservation')
        )
            ->leftJoin('reservation', 'users.id', '=', 'reservation.UserID')
            ->leftJoin('payment', 'reservation.ReservationID', '=', 'payment.ReservationID')
            ->where('users.roles', '=', 'User')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at')
            ->orderBy('total_reservations', 'desc')
            ->paginate(10);




        return view('admin.customers.index', compact('customers'));
    }




    public function show($id)
    {
        $customer = User::findOrFail($id);


        $reservations = Reservation::where('UserID', $id)
            ->with(['payment', 'cartItems.menuItem'])
            ->orderBy('CreatedAt', 'desc')
            ->get();


        $stats = [
            'total_reservations' => $reservations->count(),
            'total_spent' => $reservations->sum(function ($reservation) {
                return $reservation->payment ? $reservation->payment->Amount : 0;
            }),
            'avg_spending' => $reservations->count() > 0 ?
                ($reservations->sum(function ($reservation) {
                    return $reservation->payment ? $reservation->payment->Amount : 0;
                }) / $reservations->count()) : 0,
            'favorite_items' => DB::table('reservation_item')
                ->join('menuitem', 'reservation_item.ItemID', '=', 'menuitem.ItemID')
                ->join('reservation', 'reservation_item.ReservationID', '=', 'reservation.ReservationID')
                ->where('reservation.UserID', $id)
                ->select(
                    'menuitem.ItemName',
                    'menuitem.Price',
                    DB::raw('SUM(reservation_item.Quantity) as total_quantity'),
                    DB::raw('COUNT(DISTINCT reservation_item.ReservationID) as order_count')
                )
                ->groupBy('menuitem.ItemID', 'menuitem.ItemName', 'menuitem.Price')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get()
        ];


        return view('admin.customers.show', compact('customer', 'reservations', 'stats'));
    }
}
