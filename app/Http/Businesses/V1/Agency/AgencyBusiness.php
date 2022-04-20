<?php

namespace App\Http\Businesses\V1\Agency;

use App\Exceptions\V1\DomainException;
use App\Http\Services\V1\Agency\AgencyDomainService;
use App\Http\Services\V1\Agency\AgencyService;
use App\Http\Services\V1\Agency\PortalSettingService;
use App\Http\Services\V1\Agency\UserService;
use App\Http\Services\V1\Agency\UserVerificationService;
use App\Http\Wrappers\SegmentWrapper;
use App\Models\AgencyDomain;
use Illuminate\Http\Request;

class AgencyBusiness
{
    public function register(Request $request)
    {
        // add role
        $request->request->remove('role');
        $request->request->add(['role' => 'Agency']);

        // create agency
        $agency = (new AgencyService())->create($request);

        $agencyName = trim($request->input('agency_name'));
        $newDomain = AgencyDomain::cleanAgencyDomainName($agencyName);

        $domain = AgencyDomain::domainsFilter($newDomain.(env('AGENCY_BASE_DOMAIN', '.agency.test')))->first();

        if ($domain) {
            throw DomainException::alreadyAvaliable();
        }

        //Agency Domain
        $domainData = new \stdClass;
        $domainData->agency_id = $agency->id;
        $domainData->domain = $newDomain;
        $domainData->type = AgencyDomain::TYPE['staging'];
        $domainData->default = true;

        AgencyDomainService::create($domainData);

        unset($request['first_name']);
        unset($request['last_name']);
        $request->merge(['first_name' => $agencyName, 'last_name' => $agencyName]);

        // create agency owner
        $user = (new UserService())->create($request, $agency, true);

        // create default portal settings
        PortalSettingService::create($user);

        //assign Role
        (new UserService())->assignUserRole($request, $user);

        //segment registration event
        SegmentWrapper::registration($user);

        (new UserVerificationService())->generateVerificationCode($user);
    }
}
