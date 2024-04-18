<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders'; // Specify the table name if it differs from the model name convention
    protected $primaryKey = "OrderId";
    public $timestamps = false; // Disable automatic timestamps
  
    protected $fillable = [
        'OrderId',
        'UserId',
        'ItemId',
        'SizeId',
        'EstimatedTimeByUser',
        'ItemTotalPrice', 
        'PaymentStatus',
        'PaymentType',
        'Subtotal',
        'Type',
        'Vattotal',
        'WhatsIncluded',
        'Quantity',
        'TotalPrice',
    ];
    protected $casts = [
        'WhatsIncluded' => 'json',  
    ];
}
