<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Conversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_currency',
        'target_currency',
        'amount',
        'converted_amount',
        'created_at',
        'updated_at',
    ];
}
