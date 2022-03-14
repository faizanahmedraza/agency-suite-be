<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AgencyDomain extends Model
{
    protected $table = "agency_domains";

    const TYPE = ['live' => 1, 'staging' => 2, 'testing' => 3, 'pending' => 4];

    protected $fillable = [
        'agency_id',
        'domain',
        'default',
        'type'
    ];

    public static function cleanDomain($domain)
    {
        $data = preg_replace('#^(http(s)?://)?w{3}\.#', '', $domain);
        return Str::slug(strtolower(preg_replace('/[^A-Za-z0-9. -]/s', '', $data)));
    }

    public static function cleanAgencyName($domain)
    {
        return preg_replace("/[^a-zA-Z0-9]+/", "", $domain);
    }

    public static function domainsFilter($query, $domain)
    {
        return $query->where('domain', $domain)->first();
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class,'agency_id','id');
    }

}
