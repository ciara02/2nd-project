<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assign_Ticket extends Model
{
    protected $table = 'assign_ticket';
    use HasFactory;

    protected $primaryKey = 'assign_id';
    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'assign_name',
        'assign_email',
        'assign_date',
    ];

}
