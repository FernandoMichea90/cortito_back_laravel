<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlDisponible extends Model
{
    protected $primaryKey='id_url_disponible'; 
    use HasFactory;


    //obtener el url disponible 

    public static  function getFirstUrlAvaible(){

        return self::where('disponible',true)->first();

    }

}
