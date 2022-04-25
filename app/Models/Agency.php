<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agency extends Model
{
    use SoftDeletes;

    protected $table = "agencies";

    protected $fillable = [
        'name',
        'status',
        'plan_id'
    ];

    const STATUS = ['pending' => 0, 'active' => 1, 'blocked' => 2];

    public function user()
    {
        return $this->hasOne(User::class,'agency_id','id');
    }

    public function domains()
    {
        return $this->hasMany(AgencyDomain::class,'agency_id','id')->orderBy('created_at');
    }

    public function defaultDomain()
    {
        return $this->domains()->where('default',true)->first()->domain;
    }
}
