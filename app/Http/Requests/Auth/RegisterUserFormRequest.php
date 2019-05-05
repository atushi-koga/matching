<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use packages\Domain\Domain\Common\Prefecture;
use packages\Domain\Domain\User\Gender;

class RegisterUserFormRequest extends FormRequest
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
            'nickname'   => ['required', 'string', 'max:20'],
            'prefecture' => ['required', Rule::in(array_keys(Prefecture::Enum()))],
            'gender'     => ['required', Rule::in(array_keys(Gender::Enum()))],
            'email'      => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * @return array
     */
    //    public function validationData(): array
    //    {
    //        $data = parent::validationData();
    //
    //        // 生年月日を連結する。
    //        if (isset($data['birth_year']) && isset($data['birth_month']) && isset($data['birth_day'])) {
    //            $data['birthday'] = $data['birth_year'] . '-' . $data['birth_month'] . '-' . $data['birth_day'];
    //        }
    //
    //        return $data;
    //    }

}