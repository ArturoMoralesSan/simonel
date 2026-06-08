<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\ProductRecipe;
use App\Models\ProductRecipeItem;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ProductRecipeRequest;
use Illuminate\Support\Str;

class RecipeController extends Controller
{
    public function index()
    {
        abort_unless(Gate::allows('view.recipes') || Gate::allows('create.recipes'), 403);

        $recipes = ProductRecipe::with(['product','items.rawMaterial'])
        ->orderBy('id', 'DESC')
        ->paginate(20);
        
        $recipesItems = collect($recipes->items());

        return view(
            'admin.recetas.index',
            compact(
                'recipes',
                'recipesItems'
            )
        );    
    }

    public function create()
    {
        abort_unless(Gate::allows('view.recipes') || Gate::allows('create.recipes'), 403);
    
        $products = Product::orderBy('name', 'ASC')->pluck('name','id');
        $rawmaterials  = RawMaterial::pluck('name','id');

        return view('admin.recetas.crear', compact('products','rawmaterials'));   
    }

    public function details($id)
    {
        abort_unless(Gate::allows('view.recipes') || Gate::allows('create.recipes'), 403);


        $recipe = ProductRecipe::with([
            'product',
            'items.rawMaterial'
        ])->findOrFail($id);

        $totalCost = 0;

        foreach ($recipe->items as $item) {
            $itemCost = $item->quantity * $item->rawMaterial->cost;
            $item->calculated_cost = $itemCost;
            $totalCost += $itemCost;
        }

        $costPerKg = 0;

        if ($recipe->yield_quantity > 0) {
            $costPerKg = $totalCost / $recipe->yield_quantity;
        }

        return view(
            'admin.recetas.detalles',
            compact(
                'recipe',
                'totalCost',
                'costPerKg'
            )
        );
    }

    public function save(ProductRecipeRequest $request)
    {
        abort_unless(Gate::allows('view.recipes') || Gate::allows('create.recipes'), 403);

        if ($request->product_recipe_id == null) {
            $product_recipe = new ProductRecipe;
        } else {
            $product_recipe = ProductRecipe::findOrFail($request->product_recipe_id);

        }

        $product_recipe->product_id = $request->product_id;
        $product_recipe->version = $request->version;
        $product_recipe->yield_quantity = $request->yield_quantity;
        $product_recipe->notes = $request->notes;
        $product_recipe->active = 1;
        $product_recipe->save();

        $product_recipe->items()->delete();

        for ($i = 1; $i <= $request->item_count; $i++) {

            $item = new ProductRecipeItem;
            $item->product_recipe_id = $product_recipe->id;
            $item->raw_material_id = $request['item'.$i.'_raw_material_id'];
            $item->quantity = $request['item'.$i.'_quantity'];
            $item->unit = $request['item'.$i.'_unit'];
            $item->waste_percent = $request['item'.$i.'_waste_percent'] ?? 0;
            $item->save();
        }

        if ($request->product_recipe_id == null) {
            alert('Se ha agregado una receta.');
        } else {
            alert('Se ha editado una receta.');
        }

        return response('', 204, [
            'Redirect-To' => url('admin/recetas/')
        ]);
    }


    public function edit($id)
    {
        abort_unless(Gate::allows('view.recipes') || Gate::allows('create.recipes'), 403);

        $recipe = ProductRecipe::with(['items.rawMaterial'])->findOrFail($id);
        $products = Product::orderBy('name', 'ASC')->pluck('name', 'id');
        $rawmaterials = RawMaterial::orderBy('name', 'ASC')->pluck('name', 'id');

        return view('admin.recetas.editar', compact('recipe','products','rawmaterials'));   
    }
}
