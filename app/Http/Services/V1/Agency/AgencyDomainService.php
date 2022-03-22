<?php


namespace App\Http\Services\V1\Agency;

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
    public static function create($data)
    {
        $agency = new AgencyDomain();
        $agency->agency_id = $data->agency_id;
        $agency->domain = $data->domain.(env('AGENCY_BASE_DOMAIN','.agency.test'));
        $agency->type = $data->type;
        $agency->default = $data->default;
        $agency->save();

        if (!$agency) {
            throw FailureException::serverError();
        }

        return $agency;
    }

    public static function update(AgencyDomain $agencyDomain,$domain)
    {
        $agencyDomain->domain = $domain . (env('AGENCY_BASE_DOMAIN', '.agency.test'));
        $agencyDomain->save();

        if (!$agencyDomain) {
            throw FailureException::serverError();
        }

        return $agencyDomain;
    }
}
