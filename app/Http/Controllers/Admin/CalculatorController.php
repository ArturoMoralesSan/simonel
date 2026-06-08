<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Type;
use App\Models\Cut;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;

class CalculatorController extends Controller
{
    public function calculator()
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);
        
        $products = Product::all();
        $types = Type::pluck('name','id');
        $cuts = Cut::all();

        return view('admin.calculadora.index', compact('products', 'types', 'cuts'));   
    }
}
