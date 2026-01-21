<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_Line extends Model
{

    use HasFactory;
     protected $table = 'product_line';
     protected $primaryKey = 'prod_id';
    public $timestamps = false;
}
