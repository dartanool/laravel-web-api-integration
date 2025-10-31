<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiService extends Model
{

    protected $table = 'api_service';
    protected $fillable = ['name', 'base_url'];
}
