<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Type;
use App\Models\Cut;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);
        $search = \Request('search');

        $query = Product::with('type', 'cut')->orderBy('created_at', 'desc');
        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'desc');
        }
        $query->get()->each->setAppends([]);
        $paginatedProducts = $query->paginate(10)->appends(request()->all());;
        $ProductsItems = Collect($paginatedProducts->items());
        $links = $paginatedProducts->links('layout.pagination');

        return view('admin.productos.index', compact('ProductsItems', 'links'));   
    }

    public function create()
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);
        $types = Type::pluck('name','id');
        $presentationsOptions = Cut::selectRaw("id, CONCAT(name, ' (', measure, ')') as name")->pluck('name', 'id');
        $presentations = Cut::selectRaw("
            id,
            CONCAT(name, ' (', measure, ')') as name,
            cost
        ")->get();

$presentationOptions = $presentations->pluck('name', 'id');
        return view('admin.productos.crear', compact('types', 'presentations', 'presentationsOptions'));   
    }

    public function save(ProductRequest $request)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('edit.products'), 403);
        
        $product = new Product;
        $product->name           = $request->name;
        $product->type_id        = $request->type_id;
        $product->cut_id         = $request->cut_id;
        $product->description    = $request->description;
        $product->vinil_cost     = $request->vinil_cost;
        $product->impresion_cost = $request->impresion_cost;
        $product->indirect_cost  = $request->indirect_cost;
        $product->subtotal       = $request->subtotal;
        $product->costo_total    = $request->costo_total;
        $product->utility        = $request->utility;
        $product->costo_venta    = $request->costo_venta;
        $product->save();

        alert('Se ha agregado un producto.');

        return response('', 204, [
            'Redirect-To' => url('admin/productos/')
        ]);
    }

    public function cloneProduct($id)
    {
        $originalProduct = Product::find($id);
        

        $newProduct = $originalProduct->replicate();
        $newProduct->save();


        // Obtener la nueva venta con los productos ya clonados para enviarlos al frontend
        $newProduct->load('type', 'cut');

        return response()->json([
            'success' => true,
            'newItem' => $newProduct
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('edit.products'), 403);
        $product = Product::find($id);
        $types = Type::pluck('name','id');
        $presentationsOptions = Cut::selectRaw("id, CONCAT(name, ' (', measure, ')') as name")->pluck('name', 'id');
        $presentations = Cut::selectRaw("
            id,
            CONCAT(name, ' (', measure, ')') as name,
            cost
        ")->get();        return view('admin.productos.editar', compact('product', 'types', 'presentations', 'presentationsOptions'));
    }


    public function update(ProductRequest $request, $id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('edit.products'), 403);

        $product = Product::find($id);
        $product->name           = $request->name;
        $product->type_id        = $request->type_id;
        $product->cut_id         = $request->cut_id;
        $product->description    = $request->description;
        $product->vinil_cost     = $request->vinil_cost;
        $product->impresion_cost = $request->impresion_cost;
        $product->indirect_cost  = $request->indirect_cost;
        $product->subtotal       = $request->subtotal;
        $product->costo_total    = $request->costo_total;
        $product->utility        = $request->utility;
        $product->costo_venta    = $request->costo_venta;
        $product->save();

        alert('Se ha actualizado un producto.');

        return response('', 204, [
            'Redirect-To' => url('admin/productos/')
        ]);
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.products') || Gate::allows('create.products'), 403);

        $product = Product::find($id);
        $product->delete();

        return response('', 204);

    }
}
