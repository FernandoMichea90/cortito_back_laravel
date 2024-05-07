<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'password_reset_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'token',
        'refresh_token',
    ];


    

    /**
     * Find a record by token and email.
     *
     * @param  string  $token
     * @param  string  $email
     * @return \App\Models\PasswordResetToken|null
     */
    public static function findByTokenAndEmail($token)
    {
        
        return self::where('token', $token)
                   ->first();
                   
    }
}
