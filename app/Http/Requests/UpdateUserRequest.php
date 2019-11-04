<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $res = [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
        ];
        if (is_object($this->user)) {
            $res['login'] = 'required|string|max:255|unique:users,id,'. $this->user->id;
        } else {
            $res['login'] = 'required|string|max:255|unique:users';
        }
        return $res;
    }
}
