<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryAddress extends Model
{
    use  HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'temporary_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id', 
        'user_id',
        'city',
        'district',
        'ward',
        'apartment_number',
        'transport_fee'
    ];
}
