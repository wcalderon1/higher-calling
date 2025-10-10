<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Allow only authenticated users to submit this form.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Validation rules for updating the profile.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Existing Breeze fields
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            // NEW profile fields
            'display_name' => ['required','string','min:2','max:80','regex:/^[\pL\pN\s\-\_\.]+$/u'],
            'bio'          => ['nullable','string','max:500'],
            'avatar'       => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'], // size in KB
        ];
    }

    /**
     * Custom messages (optional but helpful).
     */
    public function messages(): array
    {
        return [
            'display_name.regex' => 'Only letters, numbers, spaces, dash (-), underscore (_), and dot (.) are allowed.',
        ];
    }
}

