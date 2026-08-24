<?php

namespace App\Http\Requests;

use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'min:3'],
            'content' => ['required'],
            'author_id' => ['required', 'exists:authors,id'],
            'g-recaptcha-response' => [new Recaptcha],
        ];
    }
}
