<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'position' => ['nullable', 'string', 'max:255'],
            'project_name' => ['nullable', 'string', 'max:255'],
            'project_company' => ['nullable', 'string', 'max:255'],
            'contractor_name' => ['nullable', 'string', 'max:255'],
            'contractor_supervisor_name' => ['nullable', 'string', 'max:255'],
            'contractor_supervisor_title' => ['nullable', 'string', 'max:255'],
            'project_supervisor_name' => ['nullable', 'string', 'max:255'],
            'project_supervisor_title' => ['nullable', 'string', 'max:255'],
        ];
    }
}

