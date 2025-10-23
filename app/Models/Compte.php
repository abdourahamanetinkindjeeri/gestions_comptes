<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compte extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    public $keyType = 'string';

    protected $fillable = [
        'numero_compte',
        'type',
        'solde_initial',
        'devise',
        'statut',
        'client_id',
        'metadata',
    ];

    protected $casts = [
        'solde_initial' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Relation vers Client.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation vers Transactions.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
