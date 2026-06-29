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

        $query = ProductLot::with([
                'product.manufactured',
                'order',
                'warehouse'
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    // Si ProductLot tiene un código o lote
                    $q->where('lot_number', 'like', "%{$search}%")

                        ->orWhereHas('product.manufactured', function ($manufactured) use ($search) {
                            $manufactured->where('name', 'like', "%{$search}%");
                        })

                        ->orWhereHas('warehouse', function ($warehouse) use ($search) {
                            $warehouse->where('name', 'like', "%{$search}%");
                        })

                        ->orWhereHas('order', function ($order) use ($search) {
                            $order->where('order_number', 'like', "%{$search}%");
                            // o ->where('folio', 'like', "%{$search}%");
                            // o ->where('order_number', 'like', "%{$search}%");
                        });

                });
            })
            ->orderByDesc('created_at');

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
