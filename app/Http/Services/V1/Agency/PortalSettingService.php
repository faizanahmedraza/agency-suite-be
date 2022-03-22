<?php


namespace App\Http\Services\V1\Agency;

use App\Exceptions\V1\DomainException;
use App\Exceptions\V1\FailureException;
use App\Exceptions\V1\ModelException;
use App\Http\Services\V1\CloudinaryService;
use App\Models\Agency;
use App\Models\AgencyDomain;
use App\Models\PortalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalSettingService
{
    public static function update(Request $request)
    {
        if ($request->has('name') && !empty('name')) {
            $agency = Agency::where('id', app('agency')->id)->first();

            if (!$agency) {
                throw ModelException::dataNotFound();
            }

            $agency->name = $request->name;
            $agency->save();

            if (!$agency) {
                throw FailureException::serverError();
            }

            $newDomain = AgencyDomain::cleanAgencyName($request->name);

            $domain = AgencyDomain::domainsFilter($agency->domains, $newDomain);

            if ($domain) {
                throw DomainException::alreadyAvaliable();
            }

            //Agency Domain
            $agencyDomain = AgencyDomain::where('default', true)->where('agency_id', app('agency')->id)->first();
            $agencyDomain->agency_id = $agency->id;
            $agencyDomain->domain = $newDomain . (env('AGENCY_BASE_DOMAIN', '.agency.test'));
            $agencyDomain->updated_by = Auth::id();
            $agencyDomain->save();

            if (!$agencyDomain) {
                throw FailureException::serverError();
            }
        }
        $setting = new PortalSetting();
        if ($request->has('logo') && !empty($request->logo)) {
            $setting->logo = CloudinaryService::upload($request->logo)->secureUrl;
        }
        if ($request->has('favicon') && !empty($request->favicon)) {
            $setting->favicon = CloudinaryService::upload($request->favicon)->secureUrl;
        }
        $setting->primary_color = $request->primary_color;
        $setting->save();

        if (!$setting) {
            throw FailureException::serverError();
        }
    }
}
