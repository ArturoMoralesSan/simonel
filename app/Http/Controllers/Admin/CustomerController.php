<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserCustomerRequest;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerProductPrice;
use App\Models\Product;
use App\Models\CustomerCreditSetting;
use Illuminate\Support\Facades\Gate;
use Hash;
use Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index()
    {
        abort_unless(
            Gate::allows('view.customers') ||
            Gate::allows('create.customers'),
            403
        );

        $search = request('search');

        $users = Customer::with([
            'user' => function ($query) {
                $query->withCount('sales');
            },
            'seller',
        ])
        ->when(
            !Auth::user()->isSuperAdmin() && !Auth::user()->isAdmin(),
            function ($query) {
                $query->where('seller_id', Auth::id());
            }
        )
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'LIKE', "%{$search}%")
                    ->orWhere('rfc', 'LIKE', "%{$search}%")
                    ->orWhere('trade_name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        })
        ->get();

        return view('admin.clientes.index', compact('users'));
    }

    public function details($id)
    {
        abort_unless(
            Gate::allows('view.customers') || Gate::allows('create.customers'),
            403
        );

        $customer = Customer::with([
            'user',
            'creditSetting',
            'creditAuthorizations.authorizer',
            'productPrices.product.manufactured'
        ])->findOrFail($id);
        
        $isSuperAdmin = auth()->user()->isSuperAdmin() || auth()->user()->isAdmin();

        return view(
            'admin.clientes.detalles',
            compact('customer', 'isSuperAdmin')
        );
    }

    public function create()
    {
        abort_unless(Gate::allows('view.customers') || Gate::allows('create.customers'), 403);


        $isSuperAdmin = auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isAdmin();
        $sellers = User::whereHas('role', function ($query) {
            $query->where('key_name', 'vendedores');
        })
        ->selectRaw("id, CONCAT(name, ' ', last_name) AS full_name")
        ->orderBy('name')
        ->pluck('full_name', 'id');
        $products = Product::with('manufactured')
            ->get()
            ->pluck('manufactured.name', 'id');
        $regimenLabel = collect([
            'resico_pf' => 'RESICO - Persona Física',
            'sueldos_salarios' => 'Sueldos y Salarios',
            'actividad_empresarial' => 'Actividad Empresarial y Profesional',
            'arrendamiento' => 'Arrendamiento',
            'agricola_ganadera' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
            'enajenacion_bienes' => 'Enajenación de Bienes',
            'adquisicion_bienes' => 'Adquisición de Bienes',
            'demas_ingresos' => 'Demás Ingresos',
            'dividendos' => 'Ingresos por Dividendos',
            'intereses' => 'Intereses',
            'premios' => 'Ingresos por Obtención de Premios',
            'plataformas_tecnologicas' => 'Plataformas Tecnológicas',

            'resico_pm' => 'RESICO - Persona Moral',
            'general_pm' => 'Régimen General de Ley - Persona Moral',
            'fines_no_lucrativos' => 'Personas Morales con Fines No Lucrativos',
        ]);

        return view('admin.clientes.crear', compact('isSuperAdmin', 'sellers', 'regimenLabel', 'products'));
    }


    public function save(StoreUserCustomerRequest $request)
    {
        abort_unless(
            Gate::allows('view.customers') || Gate::allows('create.customers'),
            403
        );


        if ($request->customer_id == null) {
            $user = new User;
        } else {
            $user = User::find($request->user_id);
        }


        $seller_id = Auth::id();

        if (
            Auth::user()->isSuperAdmin() ||
            (Auth::user()->isAdmin() && $request->filled('seller_id'))
        ) {
            $seller_id = $request->seller_id;
        }


        $user->name    = $request->business_name;
        $user->email   = $request->email;
        $user->role_id = 2;
        $user->save();


        if ($request->customer_id == null) {
            $customer = new Customer;
        } else {
            $customer = Customer::find($request->customer_id);
        }

        $customer->user_id       = $user->id;
        $customer->business_name = $request->business_name;
        $customer->rfc           = $request->rfc;
        $customer->trade_name    = $request->trade_name;
        $customer->tax_regime    = $request->tax_regime;
        $customer->customer_type = $request->customer_type;
        $customer->phone         = $request->phone;
        $customer->email         = $request->email;

    

        $customer->street          = $request->street;
        $customer->ext_number      = $request->ext_number;
        $customer->int_number      = $request->int_number;
        $customer->between_streets = $request->between_streets;
        $customer->and_street      = $request->and_street;
        $customer->country         = $request->country ?? 'MEX. México';
        $customer->state           = $request->state;
        $customer->municipality    = $request->municipality;
        $customer->population      = $request->population;
        $customer->colony          = $request->colony;
        $customer->postal_code     = $request->postal_code;

        

        $customer->seller_id = $seller_id;

        $customer->save();


        $customer->productPrices()->delete();

        for ($i = 1; $i <= ($request->price_count ?? 0); $i++) {

            $product_id = $request->input(
                'price' . $i . '_product_id'
            );

            $price = $request->input(
                'price' . $i . '_price'
            );

            if ($product_id && $price !== null && $price !== '') {

                $productPrice = new CustomerProductPrice;

                $productPrice->customer_id = $customer->id;
                $productPrice->product_id  = $product_id;
                $productPrice->price       = $price;

                $productPrice->save();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Crédito
        |--------------------------------------------------------------------------
        */

        if ($request->credit == 1) {

            CustomerCreditSetting::updateOrCreate(
                [
                    'customer_id' => $customer->id
                ],
                [
                    'enabled'       => $request->credit,
                    'credit_limit'  => $request->credit_limit,
                    'credit_days'   => $request->credit_days,
                    'block_on_debt' => 0,
                    'authorized_by' => Auth::id(),
                    'authorized_at' => Carbon::now()
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Mensaje
        |--------------------------------------------------------------------------
        */

        if ($request->customer_id == null) {
            alert('Se ha agregado un cliente.');
        } else {
            alert('Se ha editado un cliente.');
        }

        /*
        |--------------------------------------------------------------------------
        | Redirección
        |--------------------------------------------------------------------------
        */

        return response('', 204, [
            'Redirect-To' => url('admin/agregar-cliente/')
        ]);
    }

    public function edit($id)
    {
        abort_unless(Gate::allows('view.customers') || Gate::allows('create.customers'), 403);

        $user = Customer::with(['creditSetting', 'productPrices'])->findOrFail($id);    
        $isSuperAdmin = auth()->user()->isSuperAdmin() || auth()->user()->isAdmin();

        $products = Product::with('manufactured')
            ->get()
            ->pluck('manufactured.name', 'id');
        $sellers = User::whereHas('role', function ($query) {
            $query->where('key_name', 'vendedores');
        })
        
        ->selectRaw("id, CONCAT(name, ' ', last_name) AS full_name")
        ->orderBy('name')
        ->pluck('full_name', 'id');

        $regimenLabel = collect([
            'resico_pf' => 'RESICO - Persona Física',
            'sueldos_salarios' => 'Sueldos y Salarios',
            'actividad_empresarial' => 'Actividad Empresarial y Profesional',
            'arrendamiento' => 'Arrendamiento',
            'agricola_ganadera' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
            'enajenacion_bienes' => 'Enajenación de Bienes',
            'adquisicion_bienes' => 'Adquisición de Bienes',
            'demas_ingresos' => 'Demás Ingresos',
            'dividendos' => 'Ingresos por Dividendos',
            'intereses' => 'Intereses',
            'premios' => 'Ingresos por Obtención de Premios',
            'plataformas_tecnologicas' => 'Plataformas Tecnológicas',

            'resico_pm' => 'RESICO - Persona Moral',
            'general_pm' => 'Régimen General de Ley - Persona Moral',
            'fines_no_lucrativos' => 'Personas Morales con Fines No Lucrativos',
        ]);

        return view('admin.clientes.editar', compact('user', 'isSuperAdmin', 'sellers', 'regimenLabel', 'products'));
    }

    public function destroy($id)
    {
        abort_unless(Gate::allows('view.customers') || Gate::allows('create.customers'), 403);

        if (Auth::user()->id !== $id) {
            $customer = Customer::find($id);
            $user = User::find($customer->user_id);
            $user->delete();
            alert('Se ha eliminado un cliente.');
        }

        alert('No se ha podido eliminar un cliente.');
        return response('', 204);

    }
}
