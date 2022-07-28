<?php

namespace App\Http\Businesses\V1\Agency;

use App\Exceptions\V1\DomainException;
use App\Http\Services\V1\Agency\AgencyDomainService;
use App\Http\Services\V1\Agency\AgencyService;
use App\Http\Services\V1\Agency\PortalSettingService;
use App\Http\Services\V1\Agency\UserService;
use App\Http\Services\V1\Agency\UserVerificationService;
use App\Http\Wrappers\SegmentWrapper;
use App\Jobs\VerificationEmailJob;
use App\Mail\VerificationEmail;
use App\Models\AgencyDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        $agencyDomain = AgencyDomainService::create($domainData);

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

        $userVerification = (new UserVerificationService())->generateVerificationCode($user);

        //send email here
        $data = [
            'name' => $user->first_name,
            'url' => $agencyDomain->domain.'/verify/'.$userVerification->verification_code,
            'to' => $user->username,
            'subject' => 'Verify your account'
        ];

        dispatch(new VerificationEmailJob($data))->onQueue('emails');
    }
}
