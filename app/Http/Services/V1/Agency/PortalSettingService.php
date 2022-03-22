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
        if ($request->has('name') && !empty($request->name)) {
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

            AgencyDomainService::update($agencyDomain,$newDomain);
        }

        $setting = PortalSetting::where('agency_id', app('agency')->id)->where('user_id', Auth::id())->first();

        if (!$setting) {
            $setting = new PortalSetting();
        }

        if ($request->has('logo') && !empty($request->logo)) {
            $setting->logo = CloudinaryService::upload($request->logo)->secureUrl;
        }
        if ($request->has('favicon') && !empty($request->favicon)) {
            $setting->favicon = CloudinaryService::upload($request->favicon)->secureUrl;
        }
        $setting->agency_id = app('agency')->id;
        $setting->user_id = Auth::id();
        $setting->primary_color = !empty($request->primary_color) ? trim($request->primary_color) : null;
        $setting->save();

        if (!$setting) {
            throw FailureException::serverError();
        }
    }
}
