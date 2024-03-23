<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
    ];

    // Relación con la tabla short_urls
    public function shortUrls()
    {
        return $this->hasMany(ShortUrl::class);
    }
}
