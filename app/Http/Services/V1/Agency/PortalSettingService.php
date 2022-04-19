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
use Illuminate\Support\Str;

class PortalSettingService
{
    public static function first($agencyId = null)
    {
        $_agencyId = empty($agencyId) ? app('agency')->id : $agencyId;
        $setting = PortalSetting::with('agency')->where('agency_id', $_agencyId)->first();

        if (!$setting) {
            throw ModelException::dataNotFound();
        }
        return $setting;
    }

    public static function update(Request $request)
    {
        $agency = Agency::with('domains')->where('id', app('agency')->id)->first();

        if (!$agency) {
            throw ModelException::dataNotFound();
        }

        if ($request->has('name') && !empty($request->name)) {
            $agency->name = $request->name;
            $agency->save();

            if (!$agency) {
                throw FailureException::serverError();
            }
        }

        $setting = PortalSetting::where('agency_id', app('agency')->id)->first();

        if (!$setting) {
            $setting = new PortalSetting();
        }

        if ($request->has('logo') && !empty($request->logo) && !Str::contains($request->logo, ['res', 'https', 'cloudinary'])) {
            $setting->logo = CloudinaryService::upload($request->logo)->secureUrl;
        }
        if ($request->has('favicon') && !empty($request->favicon) && !Str::contains($request->favicon, ['res', 'https', 'cloudinary'])) {
            $setting->favicon = CloudinaryService::upload($request->favicon)->secureUrl;
        }
        $setting->agency_id = app('agency')->id;
        $setting->user_id = Auth::id();
        $setting->primary_color = trim($request->primary_color) ?? '';
        $setting->secondary_color = trim($request->secondary_color) ?? '';
        $setting->save();

        if (!$setting) {
            throw FailureException::serverError();
        }
        $setting->load('agency');
        return $setting;
    }

    public static function create($user)
    {
        $setting = new PortalSetting;
        $setting->agency_id = $user->agency_id;
        $setting->user_id = $user->id;
        $setting->primary_color = config('portal_settings.primary_color');
        $setting->secondary_color = config('portal_settings.secondary_color');
        $setting->logo = config('portal_settings.logo');
        $setting->favicon = config('portal_settings.favicon');
        $setting->save();
    }
}
