<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\RawMaterialLot;
use App\Models\ProductLot;

class NotificationService
{
    public function checkMaterialExpiry()
    {
        $lots = RawMaterialLot::where('expiration_date', '<=', now()->addDays(7))->where('expiration_date', '>=', now())->get();

        foreach ($lots as $lot) {
            Notification::firstOrCreate([
                'message' => "Lote de material por caducar: {$lot->material->name} (vence {$lot->expiration_date})",
            ], [
                'read' => false
            ]);
        }
    }

    public function checkProductExpiry()
    {
        $lots = ProductLot::where('expiration_date', '<=', now()->addDays(7))->where('expiration_date', '>=', now())->get();

        foreach ($lots as $lot) {
            Notification::firstOrCreate([
                'message' => "Lote de producto por caducar: {$lot->product->name} (vence {$lot->expiration_date})",
            ], [
                'read' => false
            ]);
        }
    }

    public function checkStockLow()
    {
        $products = ProductLot::where('available_quantity', '>', 0)
            ->where('available_quantity', '<=', 10)
            ->where('status', 'Disponible')
            ->with('product')
            ->get();

        foreach ($products as $lot) {

            Notification::firstOrCreate([
                'message' => "Stock bajo de producto: {$lot->product->name} (Lote {$lot->lot_number}) - {$lot->available_quantity} unidades",
            ], [
                'read' => false
            ]);
        }
    }

    public function checkStockMaterialLow()
    {
        $lots = RawMaterialLot::where('available_quantity', '>', 0)
            ->where('available_quantity', '<=', 30)
            ->where('status', 'Disponible')
            ->with('material')
            ->get();

        foreach ($lots as $lot) {

            Notification::firstOrCreate([
                'message' => "Stock bajo de material: {$lot->material->name} (Lote {$lot->lot_number}) - {$lot->available_quantity} unidades",
            ], [
                'read' => false
            ]);
        }
    }
}