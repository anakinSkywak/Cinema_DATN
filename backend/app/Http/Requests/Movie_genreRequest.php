<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Movie_genreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Quy tắc xác thực cho trường 'ten_loai_phim'
            'ten_loai_phim' => 'required|string|max:255'
        ];
    }

    /**
     * Custom error messages for validation
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            // Thông báo lỗi cho trường 'ten_loai_phim'
            'ten_loai_phim.required' => 'Hãy điền đầy đủ loại phim',
            'ten_loai_phim.string' => 'Loại phim phải là chuỗi ký tự',
            'ten_loai_phim.max' => 'Loại phim không được vượt quá 255 ký tự',
        ];
    }
}
