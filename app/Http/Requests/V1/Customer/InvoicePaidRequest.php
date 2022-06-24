<?php

namespace App\Http\Requests\V1\Customer;

use Illuminate\Validation\Rule;
use Pearl\RequestValidate\RequestAbstract;

class InvoicePaidRequest extends RequestAbstract
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'card_id' => [
                'required',
                Rule::exists('customer_card_details', 'id')->where(function ($query) {
                    $query->where('agency_id', app('agency')->id)->where('customer_id', auth()->id());
                })
            ],
            'invoice_id' => [
                'required',
                Rule::exists('customer_invoices', 'id')->where(function ($query) {
                    $query->where('agency_id', app('agency')->id)->where('customer_id', auth()->id())->where('is_paid', false);
                })
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'invoice_id.exists' => 'This invoice is already paid.'
        ];
    }
}
