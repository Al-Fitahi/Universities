<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'university_major_id' => [
                'required',
                'string',
                Rule::exists('university_majors', 'public_id')->where(fn ($query) => $query->where('published', true)),
            ],

            'F_name'  => ['required', 'string'],
            'S_name'  => ['required', 'string'],
            'Th_name' => ['required', 'string'],
            'Su_name' => ['required', 'string'],

            'phone_number'      => ['required', 'string'],
            'graduation_date'   => ['required', 'date'],
            'graduation_grade'  => ['required', 'numeric'],
            'certificate_image' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'university_major_id.required' => 'يرجى اختيار تخصص للتقديم.',
            'university_major_id.exists' => 'التخصص المطلوب للتقديم غير متاح حالياً.',
        ];
    }
}
