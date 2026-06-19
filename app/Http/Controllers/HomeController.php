<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Discount;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->take(6)->get();
        $activeDiscounts = Discount::with('service')
            ->where('active', true)
            ->whereDate('expires_at', '>=', now())
            ->orderBy('expires_at', 'asc')
            ->get();
        return view('home', compact('services', 'activeDiscounts'));
    }

    public function services()
    {
        $services = Service::with(['discounts' => function($query) {
            $query->where('active', true)
                  ->whereDate('expires_at', '>=', now());
        }])->get();
        
        return view('layanan', compact('services'));
    }
}
