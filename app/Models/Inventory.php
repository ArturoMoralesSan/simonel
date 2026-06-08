<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Notification;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'product_id', 'quantity', 'quantity_min', 'total_value'
    ];

    public static function checkStock($inventory)
    {   
        if ($inventory->quantity <= $inventory->quantity_min) { 
            Notification::create([
                'message' => "Stock bajo: {$inventory->product->name} tiene solo {$inventory->quantity} unidades",
                'read' => false
            ]);
        }
    }

    public function client() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function movements() {
        return $this->hasMany(InventoryMovement::class);
    }

    public function updateTotals() {
        $this->quantity = $this->movements()->where('type','entrada')->sum('quantity')
                        - $this->movements()->where('type','salida')->sum('quantity');
        $this->total_value = $this->quantity * ($this->template->base_price ?? 0);
        $this->save();
    }
}
