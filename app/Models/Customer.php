<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = "agency_customers";


    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class,'agency_id','id');
    }
}
