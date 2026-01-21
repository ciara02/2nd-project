<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Update_Status extends Model
{
    protected $table = 'update_status';
    protected $primaryKey = 'status_id';
    public $timestamps = false;
    use HasFactory;

    protected $fillable = [
        'status',
        'ticket_id',
        'update_date',
        'update_by',
        'Custom_Status'
    ];
}
