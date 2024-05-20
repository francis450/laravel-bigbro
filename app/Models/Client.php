<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Client extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'kra_pin'
    ];

    public function invoice()
    {
        return $this->hasMany(Invoice::class);
    }
}
