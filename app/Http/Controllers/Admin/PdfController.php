<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use Auth;
use App\Models\Sale;
use App\Models\User;

use Luecano\NumeroALetras\NumeroALetras;

class PdfController extends Controller
{


    public function customers(Request $request, $id)
{
    $user = User::with('customer')->findOrFail($id);

    $dateNow = Carbon::now();

    $start_date = $request->start_date
        ?: $dateNow->copy()->subDays(5)->format('Y-m-d');

    $end_date = $request->end_date
        ?: $dateNow->format('Y-m-d');

    $salesQuery = $user->sales()
    ->with(['products.product.manufactured', 'customer'])
    ->where('status', 'paid')
    ->orderBy('created_at', 'desc');

    $salesQuery->whereBetween('created_at', [
        Carbon::parse($start_date)->startOfDay(),
        Carbon::parse($end_date)->endOfDay()
    ]);

    $sales = $salesQuery->get();

    // 🧠 MAPEAR PRODUCTOS DESDE SALEPRODUCT
    $sales->each(function ($sale) {

        $sale->products_list = $sale->products
            ->map(function ($sp) {
                return ($sp->product->manufactured->name ?? 'Sin producto');
            })
            ->implode(', ');
    });

    $totalGeneral = $sales->sum('total_with_iva');

    $pdf = PDF::loadView('admin.pdf.customer_sales', [
        'user' => $user,
        'customer' => $user->customer,
        'sales' => $sales,
        'totalGeneral' => $totalGeneral,
        'start_date' => $start_date,
        'end_date' => $end_date
    ]);

    return $pdf->stream("user-{$user->id}.pdf");
}

    public function pdfSale($id)
    {
        $sale = Sale::with([
            'user.customer',
            'payments',
            'products.product.manufactured',
            'products.lots.productLot',
        ])->findOrFail($id);

        $pdf = PDF::loadView(
            'admin.pdf.notesale',
            compact('sale')
        );

        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream(
            'Venta-' . $sale->id . '.pdf'
        );

        // return $pdf->download('Venta-' . $sale->id . '.pdf');
    }
}
