<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Illuminate\Database\Eloquent\Model;

class PortalSetting extends Model
{
    use UserAuditTrait;

    protected $table = "portal_settings";

    protected $fillable = [
        'logo',
        'favicon',
        'primary_color',
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
