<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'shop_id' => 'required|exists:shops,id',
            'payment_category_id' => 'required|exists:payment_states,id',
            'bill_id' => 'required|exists:bills,id',
            'sum' => 'required',
            'type' => [
                'required',
                Rule::in(['1', '2'])
            ]
        ];
    }
}
