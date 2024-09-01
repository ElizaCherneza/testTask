<?php

namespace App\Http\Requests\Guest;
use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["string", "required"],
            "surname" => ["string", "required"],
            "phone" => ["string", "required"],
            "email" => ["string", "required"],
            "country" => ["string", "nullable"],
        ];
    }
}