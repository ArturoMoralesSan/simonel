<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductLot;
use App\Models\RawMaterialLot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ExpirationController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            Gate::allows('view.productionorders') ||
            Gate::allows('create.productionorders'),
            403
        );

        $type = $request->type;
        $status = $request->status;

        /*
        |--------------------------------------------------------------------------
        | Materias primas
        |--------------------------------------------------------------------------
        */

        $rawMaterialLots = RawMaterialLot::with([
            'material'
        ])
        ->whereNotNull('expiration_date')
        ->get()
        ->map(function ($lot) {

            $days = Carbon::today()->diffInDays(
                Carbon::parse($lot->expiration_date),
                false
            );

            return [
                'type' => 'Materia Prima',
                'name' => $lot->material->name ?? '-',
                'lot_number' => $lot->lot_number,
                'entry_date' => $lot->entry_date,
                'expiration_date' => $lot->expiration_date,
                'quantity' => $lot->available_quantity,
                'days_remaining' => $days,
                'status' => $this->getStatus($days),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Productos terminados
        |--------------------------------------------------------------------------
        */

        $productLots = ProductLot::with([
            'product.manufactured'
        ])
        ->whereNotNull('expiration_date')
        ->get()
        ->map(function ($lot) {

            $days = Carbon::today()->diffInDays(
                Carbon::parse($lot->expiration_date),
                false
            );

            return [
                'type' => 'Producto Terminado',
                'name' => $lot->product->manufactured->name ?? '-',
                'lot_number' => $lot->lot_number,
                'entry_date' => $lot->production_date,
                'expiration_date' => $lot->expiration_date,
                'quantity' => $lot->available_quantity,
                'days_remaining' => $days,
                'status' => $this->getStatus($days),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Unir información
        |--------------------------------------------------------------------------
        */

        $lots = collect()
            ->merge($rawMaterialLots)
            ->merge($productLots);

        /*
        |--------------------------------------------------------------------------
        | Filtro tipo
        |--------------------------------------------------------------------------
        */

        if ($type == 'mp') {

            $lots = $lots->where(
                'type',
                'Materia Prima'
            );
        }

        if ($type == 'pt') {

            $lots = $lots->where(
                'type',
                'Producto Terminado'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filtro estado
        |--------------------------------------------------------------------------
        */

        if ($status == 'expired') {

            $lots = $lots->filter(function ($lot) {
                return $lot['days_remaining'] < 0;
            });
        }

        if ($status == '7days') {

            $lots = $lots->filter(function ($lot) {

                return
                    $lot['days_remaining'] >= 0 &&
                    $lot['days_remaining'] <= 7;
            });
        }

        if ($status == '30days') {

            $lots = $lots->filter(function ($lot) {

                return
                    $lot['days_remaining'] >= 0 &&
                    $lot['days_remaining'] <= 30;
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Ordenar
        |--------------------------------------------------------------------------
        */

        $lots = $lots
            ->sortBy('expiration_date')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Contadores dashboard
        |--------------------------------------------------------------------------
        */

        $expiredCount = $lots
            ->where('status', 'Vencido')
            ->count();

        $next7DaysCount = $lots
            ->filter(function ($lot) {

                return
                    $lot['days_remaining'] >= 0 &&
                    $lot['days_remaining'] <= 7;
            })
            ->count();

        $next30DaysCount = $lots
            ->filter(function ($lot) {

                return
                    $lot['days_remaining'] >= 0 &&
                    $lot['days_remaining'] <= 30;
            })
            ->count();

        return view(
            'admin.caducidades.index',
            compact(
                'lots',
                'type',
                'status',
                'expiredCount',
                'next7DaysCount',
                'next30DaysCount'
            )
        );
    }

    private function getStatus($days)
    {
        if ($days < 0) {
            return 'Vencido';
        }

        if ($days <= 7) {
            return 'Próximo a vencer';
        }

        if ($days <= 30) {
            return 'Atención';
        }

        return 'Vigente';
    }
}
