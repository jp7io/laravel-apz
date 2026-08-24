<?php

namespace App\Http\Requests;

use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;

class AuthorRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email:filter'],
            'g-recaptcha-response' => [new Recaptcha],
        ];
    }
}
