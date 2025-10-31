<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenType extends Model
{

    protected $table = 'token_types';
    protected $fillable = ['name', 'description'];

}
