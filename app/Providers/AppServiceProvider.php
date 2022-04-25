<?php

namespace App\Providers;

use App\Exceptions\V1\DomainException;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('agency', function () {
            $request = app(\Illuminate\Http\Request::class);
            $hasDomainName = $request->headers->has('Domain');
            $domainName ='';
            if ($hasDomainName) {
                $domainName = $request->header('Domain');
            }
            $agencyDomain = \App\Models\AgencyDomain::where('domain',$domainName)->first();

            $agency = (object)[];
            if($agencyDomain){
                $agency->id = $agencyDomain->agency_id;
                $agency->domain_name = $agencyDomain->domain;
                $agency->name=optional($agencyDomain->agency)->name;
                $agency->status=optional($agencyDomain->agency)->status;
            }
            return $agency;
        });
    }
}
