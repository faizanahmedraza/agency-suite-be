<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $table = "agencies";

    protected $fillable = [
        'name'
    ];

    public function user()
    {
        return $this->hasOne(User::class,'agency_id','id');
    }

    public function domains()
    {
        return $this->hasMany(AgencyDomain::class,'agency_id','id')->orderBy('created_at');
    }
}
