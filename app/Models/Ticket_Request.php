<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket_Request extends Model
{
    use HasFactory;
     protected $table = 'ticket_request';

    // Primary key
    protected $primaryKey = 'ticket_id';

    // If your primary key is NOT auto-incrementing, add this:
    // public $incrementing = false;

    // If the primary key is NOT an integer (e.g., string UUID), also add:
    // protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ticket_number',
        'company_name',
        'contact_name',
        'contact_email',
        'contact_number',
        'concern',
        'attachment',
        'date_created',
        'created_by',
        'date_resolved',
        'resolved_by',
        'engineer',
        'contract',
        'prod_id',
        'prod_line_id',
        'prod_line_project_id',
        'status',
        'solution',
        'refence_ticket',
        'prod_contract',
        'Reseller_name',
        'License',
        'serial_number',
        'purchase_date',
        'address',
        'declined_reason',
        'severity',
        'severity_reason',
        'partner_name',
        'sapdatabase_name',
        'infrastructure',
        'sap_version',
        'closed_by',
    ];
    public function engineerAssigned()
    {
        return $this->hasOne(Assign_Ticket::class, 'ticket_id', 'ticket_id')
                    ->latest('assign_date');
    }
    public function productLine()
    {
        return $this->belongsTo(Product_Line::class, 'prod_id', 'prod_id');
    }
    public function remarksLogs()
    {
        return $this->hasMany(Remarks_Logs::class, 'ticket_id', 'ticket_id');
    }
    public function updateStatus()
    {
        return $this->hasMany(Update_Status::class, 'ticket_id', 'ticket_id');
    }
    public function assignments()
{
    return $this->hasMany(Assign_Ticket::class, 'ticket_id', 'ticket_id');
}

}