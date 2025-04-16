<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';
     protected $fillable = [
        'user_id',
        'type',
        'amount',
        'status',
        'target_transfer',
        'account_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    use HasFactory;
}
