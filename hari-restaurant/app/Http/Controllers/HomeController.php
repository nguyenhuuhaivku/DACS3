<?php


namespace App\Http\Controllers;


use App\Models\user\MenuItem;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public function index()
    {
        $featuredItems = MenuItem::whereIn('status', ['Đặc biệt', 'Món mới', 'Phổ biến'])
            ->where('Available', 1)
            ->get()
            ->groupBy('status');

        return view('user.home', compact('featuredItems'));
    }
}
