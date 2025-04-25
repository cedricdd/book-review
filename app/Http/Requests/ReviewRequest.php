<?php

namespace App\Http\Requests;

use App\Constants;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'review' => "bail|required|min:" . Constants::REVIEW_MIN_LENGTH . "|max:" . Constants::REVIEW_MAX_LENGTH,
            'rating' => "bail|required|numeric|min:" . Constants::REVIEW_MIN_RATING . "|max:" . Constants::REVIEW_MAX_RATING,
        ];
    }
}
