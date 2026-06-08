<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TemplateRequest;
use App\Models\Type;
use App\Models\Cut;
use App\Models\Product;
use App\Models\Payment;
use App\Models\ProductTemplate;
use App\Models\Inventory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;

class TemplateController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $search = \Request('search');

        $query = ProductTemplate::with('product', 'cut')
            ->orderBy('created_at', 'DESC');
            
        if ($search) {
            $query->where('product_name', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'desc');
        }
        
        $query->get()->each->setAppends([]);
        $paginated = $query->paginate(10)->appends(request()->all());

        $templates = [
            'items' => collect($paginated->items()),
            'links' => $paginated->links('layout.pagination')
        ];

        return view('admin.plantillas.index',compact('templates'));
    }


    public function create()
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);
        
        $products = Product::all();
        $types = Type::pluck('name','id');
        $cuts = Cut::all();

        return view('admin.plantillas.crear', compact('products', 'types', 'cuts'));   
    }

    public function save(TemplateRequest $request)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);
        $template = new ProductTemplate;

        // Campos básicos comunes
        $template->product_name = $request->product_name;
        $template->base_price = $request->base_price;
        $template->profit_percentage = $request->profit_percentage;
        $template->has_inventory = $request->has_inventory;

        if ($request->mode === "advanced") {
            // Solo si es avanzado se guardan estos
            $template->product_id = $request->product_id;
            $template->cut_id = $request->cut_id;
            $template->width = $request->width;
            $template->height = $request->height;
            $template->quantity_product = $request->quantity_product;
        }

        $template->save();

        alert('Se ha agregado una plantilla');
        
        return response('', 204, [
            'Redirect-To' => url('admin/plantillas/')
        ]);  
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $template = ProductTemplate::with('product.type')->find($id);           
        $products = Product::all();
        $types = Type::pluck('name','id');
        $cuts = Cut::all();
        $assigned_products = ProductTemplate::with('product')->find($id);

        return view('admin.plantillas.editar', compact('template','products', 'types', 'cuts', 'assigned_products'));
    }

    public function update(TemplateRequest $request, $id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);
        
        $template = ProductTemplate::find($id);
         // Campos básicos comunes
        $template->product_name = $request->product_name;
        $template->base_price = $request->base_price;
        $template->profit_percentage = $request->profit_percentage;
        $template->has_inventory = $request->has_inventory;

        if ($request->mode === "advanced") {
            // Solo si es avanzado se guardan estos
            $template->product_id = $request->product_id;
            $template->cut_id = $request->cut_id;
            $template->width = $request->width;
            $template->height = $request->height;
            $template->quantity_product = $request->quantity_product;
        }

        $template->save();

        alert('Se ha editado una plantilla');
        
        return response('', 204, [
            'Redirect-To' => url('admin/plantillas/')
        ]);  
    }

    public function delete($id)
    {
        abort_unless(Gate::allows('view.quotations') || Gate::allows('create.quotations'), 403);

        $template = ProductTemplate::find($id);
        $template->delete();
        
        return response('', 204);
    }
}
