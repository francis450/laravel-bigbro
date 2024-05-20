<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'user_id',
        'total',
        'note'
    ];
    
    // invoice has multiple Payment models
    public function payment()
    {
        return $this->hasMany(Payment::class);
    }
    
    // invoice belongs to a particular Client Model
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // invoice has multiple Item models
    public function item()
    {
        return $this->hasMany(Item::class);
    }

    // Invoice belongs to a particular User Model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
