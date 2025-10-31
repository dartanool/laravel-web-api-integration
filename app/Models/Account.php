<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';
    protected $fillable = ['company_id','name'];

    public function company(){
        return $this->belongsTo(Company::class);
    }


}
