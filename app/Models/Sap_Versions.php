<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sap_Versions extends Model
{
    use HasFactory;
     protected $table = 'sap_versions';
     protected $primaryKey = 'version_id';
    public $timestamps = false;
}
