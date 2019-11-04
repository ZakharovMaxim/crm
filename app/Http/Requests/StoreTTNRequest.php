<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTTNRequest extends FormRequest
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
        $rules  = [
            'key' => 'required',
            'delivery_type' => 'required',
            'date' => 'required',
            'payer' => 'required',
            'estimated_price' => 'required',
            'recipient_name' => 'required',
            'recipient_surname' => 'required',
            'recipient_fathername' => 'required',
            'recipient_phone' => 'required',
            'description' => 'required',
            'weight' => 'required',
            'size_x' => 'required',
            'size_y' => 'required',
            'size_z' => 'required',
            'sender' => 'required',
            'sender_contact_ref' => 'required',
            'sender_city_ref' => 'required',
            'sender_warehouse_ref' => 'required',
            'sender_phone' => 'required',
        ];
        $delivery_type = $this->input('delivery_type');
        if($delivery_type == 1)
        {
            $rules['recipient_city_ref'] = 'required';
            $rules['recipient_warehouse_ref'] = 'required';
        } else if ($delivery_type == 2){
            $rules['recipient_city_name'] = 'required';
            $rules['recipient_address'] = 'required';
            $rules['recipient_house'] = 'required';
            $rules['recipient_flat'] = 'required';
        }
        return $rules;
    }
}
