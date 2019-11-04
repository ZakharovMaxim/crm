<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockActionRequest extends FormRequest
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
        $rules = [
            'to_stock' => 'required|exists:stocks,id'
        ];
        if ($this->input('type') == 3) {
            $rules['from_stock'] = 'required|exists:stocks,id';
        }
        return $rules;
    }
}
