<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\Order;
use Illuminate\Http\Request;
use App\Models\User, App\Http\Models\Product;
class DashboardController extends Controller
{
    public function __Construct(){
    	$this->middleware('auth');
    	$this->middleware('isadmin');
    }

    public function getDashboard(){
    	$users = User::count();
    	$products = Product::where('status', '1')->count();
		$ordenes = Order::select('total')->whereNotIn('status', [0, 1, 100])->whereBetween('paid_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->get();
		$ordenes_mes = Order::select('total')->whereNotIn('status', [0, 1, 100])->whereBetween('paid_at', [date('Y-m-01 00:00:00'), date('Y-m-t 23:59:59')])->get();
		$cantidad_ordenes = count($ordenes);
		$facturado_hoy = 0;
		foreach($ordenes as $ord){
			$facturado_hoy += $ord->total;
		}

		$facturado_mes = 0;
		foreach($ordenes_mes as $ord){
			$facturado_mes += $ord->total;
		}

		$data = [
			'users' => $users, 
			'products' => $products, 
			'ordenes' => $cantidad_ordenes, 
			'facturado_hoy' => number_format($facturado_hoy, 0, '', '.'),
			'facturado_mes' => number_format($facturado_mes, 0, '', '.')
		];
    	return view('admin.dashboard', $data);
    }
}
