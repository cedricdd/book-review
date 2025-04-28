<?php

namespace App\Http\Requests;

use App\Constants;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'bail|required|string|max:' . Constants::STRING_MAX_LENGTH,
            'author' => 'bail|required|string|max:' . Constants::STRING_MAX_LENGTH,
            'published_at' => 'bail|required|date',
            'summary' => 'bail|required|string|min:' . Constants::BOOK_SUMMARY_MIN_LENGTH . '|max:' . Constants::BOOK_SUMMARY_MAX_LENGTH,
            'cover' => [
                'bail', 
                Rule::requiredIf($this->routeIs('books.store')),
                'image',
                'mimes:' . implode(',', Constants::IMAGE_EXTENSIONS_ALLOWED),
                'max:' . Constants::BOOK_COVER_MAX_WEIGHT,
                'dimensions:min_width=' . Constants::BOOK_COVER_MIN_RES . ',max_width=' . Constants::BOOK_COVER_MAX_RES . ',min_height=' . Constants::BOOK_COVER_MIN_RES . ',max_height=' . Constants::BOOK_COVER_MAX_RES,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cover.dimensions' => Lang::get('validation.cover_dimensions'),
        ];
    }
}
