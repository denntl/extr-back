<?php

namespace App\Http\Requests\Admin\Client\ApplicationComment;

use App\Models\Application;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'answer' => 'nullable|string',
            'answer_author' => 'nullable|string',
            'author_name' => 'required|string',
            'icon' => 'required|string',
            'lang' => 'required|string',
            'likes' => 'required|integer',
            'stars' => 'required|integer|min:0|max:5',
            'text' => 'required|string',
        ];
    }
}
