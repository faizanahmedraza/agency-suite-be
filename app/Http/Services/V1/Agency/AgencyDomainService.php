<?php


namespace App\Http\Services\V1\Agency;

use App\Exceptions\V1\DomainException;
use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;
use App\Exceptions\V1\UnAuthorizedException;
use App\Exceptions\V1\UserException;

use App\Models\Agency;
use App\Models\AgencyDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use App\Helpers\TimeStampHelper;

class AgencyDomainService
{
    public static function create($data, $rootDomain = true)
    {
        $agency = new AgencyDomain();
        $agency->agency_id = $data->agency_id;
        if ($rootDomain) {
            $agency->domain = $data->domain . (env('AGENCY_BASE_DOMAIN', '.agency.test'));
        } else {
            $agency->domain = $data->domain;
        }
        $agency->type = $data->type;
        $agency->default = $data->default ?? true;
        $agency->save();

        if (!$agency) {
            throw FailureException::serverError();
        }

        return $agency;
    }

    public static function update(AgencyDomain $agency, $data)
    {
        $agency->agency_id = $data->agency_id;
        $agency->domain = $data->domain;
        $agency->type = $data->type;
        $agency->default = $data->default ?? true;
        $agency->save();

        if (!$agency) {
            throw FailureException::serverError();
        }

        return $agency->fresh();
    }

    public static function first($attribute, $value, $bypass = false)
    {
        $agencyDomain = AgencyDomain::where($attribute, $value)->first();

        if (!$agencyDomain && !$bypass) {
            throw DomainException::agencyDomainNotExist();
        }
        return $agencyDomain;
    }

    public static function markDefault($default = false)
    {
        AgencyDomain::where('agency_id', app('agency')->id)->update(['default' => $default]);
    }

    public static function customDomain()
    {
        return AgencyDomain::where('agency_id', app('agency')->id)->where('type', '!=', AgencyDomain::TYPE['staging'])->first();
    }

    public static function deleteDomain(AgencyDomain $domain)
    {
        if ($domain->type != AgencyDomain::TYPE['staging']) {
            $domain->delete();
        }
    }
}
