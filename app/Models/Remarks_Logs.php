<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remarks_Logs extends Model
{
    protected $table = 'logs';
    use HasFactory;

    protected $primaryKey = 'log_id';
    public $timestamps = false;
    protected $fillable = [
       'logs',
       'log_date',
       'ticket_id',
       'log_by'
    ];
      

}
