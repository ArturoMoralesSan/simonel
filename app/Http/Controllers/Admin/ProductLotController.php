<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductLot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use DB; 

class ProductLotController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.productlot') || Gate::allows('create.productlot'), 403);

        $search = request('search');
        $query = ProductLot::with(['product.manufactured', 'order', 'warehouse'])->orderBy('created_at', 'desc');

        if ($search) {
            $query->where('name', 'LIKE', "%$search%");
        }

        $paginatedProductLot = $query->paginate(10)->appends(request()->all());
        $ProductLotItems = Collect($paginatedProductLot->items());
        $links = $paginatedProductLot->links('layout.pagination');

        return view('admin.loteproductos.index', compact('ProductLotItems', 'links'));
    }

    public function details($id)
    {
        $lot = ProductLot::with(['product.manufactured', 'order'])->findOrFail($id);

        return view('admin.loteproductos.details',compact('lot'));
    }

    public function label($id)
    {
        $lot = ProductLot::with(['product.manufactured', 'order'])->findOrFail($id);

        return view('admin.loteproductos.etiqueta', compact('lot'));
    }
}
