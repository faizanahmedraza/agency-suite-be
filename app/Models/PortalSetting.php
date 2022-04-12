<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortalSetting extends Model
{
    use SoftDeletes;

    protected $table = "portal_settings";

    protected $fillable = [
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'agency_id',
        'user_id'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
