<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AgencyDomain extends Model
{
    use UserAuditTrait;

    protected $table = "agency_domains";

    const TYPE = ['custom' => 1, 'staging' => 2];

    protected $fillable = [
        'agency_id',
        'domain',
        'default',
        'type'
    ];

    public static function cleanAgencyDomainName($domain)
    {
        $data = preg_replace('#^(http(s)?://)?w{3}\.#', '', $domain);
        return Str::slug(strtolower(preg_replace('/[^A-Za-z0-9. -]/s', '', $data)));
    }

    public static function cleanDomain($domain): String
    {
        return preg_replace('#^(http(s)?://)?w{3}\.#', '', clean($domain));
    }

    public static function scopeDomainsFilter($filter, $domain)
    {
        return $filter->where(function ($query) use ($domain) {
            $query->where('domain', $domain);
        });
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

}
