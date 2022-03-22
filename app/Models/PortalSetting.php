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
        'agency_id'
    ];
}
