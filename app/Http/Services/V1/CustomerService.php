<?php


namespace App\Http\Services\V1;

/* Helpers */

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Helpers\TimeStampHelper;

/* Exceptions  */
use App\Exceptions\V1\FailureException;

/* Validation  */
use App\Models\UserVerification;

/* Models */
use App\Models\Customer;
use App\Models\CustomerService as CustomerServiceBridge;
use App\Models\AffiliatePlan;
use App\Models\AffiliatePayout;
use App\Models\User;
use App\Models\CustomerSetting;

class CustomerService
{
    /**
     * Create new Customer via required parameters. It will return Customer Object as Response
     *
     * @Param user_id Required
     *
     */
    public static function create($request, $user, $referred = null)
    {
        $customer = new Customer();
        $customer->user_id = $user->id;
        $customer->persona_id = $request->persona_id;
        $customer->type = (isset($request->type)) ? $request->type : Customer::TYPE['permanent'];
        $customer->tmp_password = (isset($request->password) && isset($request->type)) ? $request->password : null;
        $customer->referred_id = ($referred) ? $referred->user_id : null;
        $customer->affilate_code = self::generateAffilateCode();
        $customer->affiliate_plan_id = AffiliatePlan::getDefaultId();
        $customer->save();

        if (!$customer) {
            throw FailureException::serverError();
        }

        return $customer;
    }

    public static function first($where, $with = ['user', 'sites.container', 'sites.siteWpContent', 'billing', 'backups', 'sites.analytic'], $userFilter = [])
    {
        $customer = Customer::with($with)->where($where);

        if ($userFilter) {
            $customer->whereHas('user', function ($q) use ($userFilter) {
                $q->where($userFilter);
            });
        }

    
        return $customer->first();
    }


    public static function generateAffilateCode()
    {
        do {
            $code = Customer::createCode();
            $data = Customer::where('affilate_code', $code)->first();
        } while (!empty($data));

        return $code;
    }


    /**
     *  Generate unique code for verification and send it on email
     *
     * @Param user_id Required
     * @Param email Required
     *
     */
    public function generateVerificationCode($data)
    {
        $userVerification = new UserVerification();
        $userVerification->user_id = $data->id;
        $userVerification->verification_code =  Str::random(32);
        $userVerification->expiry = date('Y-m-d', strtotime("-30 days"));
        $userVerification->save();

        if (!$userVerification) {
            throw FailureException::serverError();
        }

        return $userVerification;
    }


    public static function disableWhiteLabel(CustomerServiceBridge $service): Bool
    {
        $service->date_expired = TimeStampHelper::getCurrentDate();
        $service->save();

        return $service->delete();
    }

    public static function enableWhiteLabel(Customer $customer): CustomerServiceBridge
    {
        $service = new CustomerServiceBridge();
        $service->customer_id = $customer->user_id;
        $service->service_id = 1;
        $service->date_purchase = TimeStampHelper::getCurrentDate();
        $service->save();

        if (!$service) {
            throw FailureException::serverError();
        }

        return $service;
    }

    public static function changeTmpPassword(Customer $customer, String $password): Customer
    {
        $customer->tmp_password = $password;
        $customer->save();

        if (!$customer) {
            throw FailureException::serverError();
        }

        return $customer;
    }

    public static function get(): Collection
    {
        return Customer::where('type', Customer::TYPE['permanent'])->get();
    }

    public static function verifyAffiliateCode($code, $with = [])
    {
        return Customer::with($with)->where('affilate_code', strtolower($code))->first();
    }


    public static function list($where, $with = ['sites'])
    {
        return Customer::with($with)->where($where)->get();
    }

    public static function pendingPayouts($username)
    {
        $affiliates = Customer::query()->with(['affiliateWallet', 'user', 'affiliatePayoutUser.customer.invoice', 'affiliatePayoutUser' => function ($q) {
            // $q->where('estimate_release_date', '<=', date('Y-m-d'));
            $q->where('status', AffiliatePayout::STATUS['pending']);
        }]);

        if ($username) {
            $affiliates->username($username);
        } else {
            $affiliates->take(100);
        }

        $affiliates->whereHas('affiliatePayoutUser', function ($q) {
            $q->where('status', AffiliatePayout::STATUS['pending']);
        });

        return $affiliates->get();
    }

    public static function updateRefererd($referred = null, $customer)
    {
        $customer->referred_id = ($referred) ? $referred->user_id : null;
        return $customer;
    }

    public static function updateProfileImage(Customer $customer, $image)
    {
        $customer->profile_image = $image;
        $customer->save();

        if (!$customer) {
            throw FailureException::serverError();
        }

        return $customer;
    }

    public static function validateUpaidLimit(User $user) : bool
    {
        // get unpaid pendding invoice count and ammount
        $invoiceSummary = InvoiceService::unPaidiInvoiceCount($user);

        /** Get user settings */
        $settings = AccountSettingService::get($user);
        
        $avoidRevoke = CustomerSetting::find($settings, 'avoid_revoke_site_launch');
        $amountLimit = CustomerSetting::find($settings, 'unpaid_invoice_amount_limit');
        $unpaidCount = CustomerSetting::find($settings, 'unpaid_invoice_count_limit');

        $amountLimit = ($amountLimit) ? (float) number_format($amountLimit->meta_value, 2, '.', '') : config('bionic_customer.unpaid_invoice_amount_limit');
        $unpaidCount = ($unpaidCount) ? intval($unpaidCount->meta_value) : config('bionic_customer.unpaid_invoice_count_limit');

        if ((!$avoidRevoke || !$avoidRevoke->meta_value) && ($invoiceSummary->amount > $amountLimit || $invoiceSummary->count > $unpaidCount)) {
            return false;
        }

        return true;
    }


    public static function find(User $user, $with = ['customer.services', 'customer.customerServices', 'site.customerProduct.cloudProvider', 'customer.billing'], $serviceProduct = null)
    {
        $user = User::with($with)->where('id', $user->id);
        
        if ($serviceProduct) {
            $with['site.customerProduct'] = function ($q) use ($serviceProduct) {
                $q->where('product_id', '!=', $serviceProduct->id);
            };
        }
         
        $user = $user->first();

        if (!$user) {
            throw ModelException::dataNotFound();
        }

        return $user;
    }

    public static function getAffiliateCustomers()
    {
        $customer = Customer::where('user_id', \Auth::user()->id);
        return $customer->with('affiliateUsers')->first();
    }
}
