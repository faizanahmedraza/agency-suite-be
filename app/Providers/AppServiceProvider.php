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
            if (!$hasDomainName) {
                throw DomainException::hostRequired();
            }
            $domainName = $request->header('Domain');
            if ($domainName == null) {
                throw DomainException::customMsg('Domain in headers can not be null.');
            }
            // $agencyDomain = \App\Models\AgencyDomain::where('domain',$request->getHost())->first();
            $agencyDomain = \App\Models\AgencyDomain::where('domain',$domainName)->first();

            if($agencyDomain == null){
                throw DomainException::agencyDomainNotExist();
            }


            $agency = (object)[];
            if($agencyDomain){
                $agency->id = $agencyDomain->agency_id;
                $agency->domain_name = $agencyDomain->domain;
                $agency->name=optional($agencyDomain->agency)->name;
            }
            return $agency;
        });
    }
}
