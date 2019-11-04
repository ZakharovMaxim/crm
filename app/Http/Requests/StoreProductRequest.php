<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'required'
        ];
        if (!$this->input('is_variation')) {
            if (is_array($this->input('photos'))) {
                $photos = count($this->input('photos'));
                foreach(range(0, $photos) as $index) {
                    $rules['photos.' . $index] = 'image|mimes:jpeg,bmp,png';
                }
            }
            $rules['sku'] = 'required';
        }
        if ($this->input('is_variant')) {
            $rules['parent_id'] = 'required|exists:products,id';
        } else {
            $rules['catalog_id'] = 'required|exists:folders,id';
        }
        return $rules;
    }
}
