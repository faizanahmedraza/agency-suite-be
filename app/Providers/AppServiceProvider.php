<?php

namespace App\Providers;

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
            $agencyDomain = \App\Models\AgencyDomain::where('domain',$request->getHost())->first();

//            $agency = new \stdClass;
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
