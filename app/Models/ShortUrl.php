<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'persona_id',
        'url_disponible_id',
        'long_url',
    ];

    // Relación con la tabla personas
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    // Relación con la tabla url_disponibles
    public function urlDisponible()
    {
        return $this->belongsTo(UrlDisponible::class);
    }
}
