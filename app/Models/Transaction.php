<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';
     protected $fillable = [
        'user_id',
        'to_user_id',
        'type',
        'amount',
        'status',
        'target_transfer',
        'account_number',
        'approved_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id'); // Relasi dengan penerima
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by'); // Relasi dengan pengguna yang meng-approve
    }
    use HasFactory;
}
