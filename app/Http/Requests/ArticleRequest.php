<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ArticleRequest extends Request
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
            'title' => 'required|min:3',
            'content' => 'required',
            'author_id' => 'required',
        ];

        if (!app()->environment('testing')) {
            $rules['g-recaptcha-response'] = 'required|recaptcha';
        }

        return $rules;
    }
}
