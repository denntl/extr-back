<?php

namespace App\Http\Requests\Admin\Manage\ApplicationComment;

use App\Models\Application;
use Illuminate\Foundation\Http\FormRequest;

class CloneRequest extends FormRequest
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
            'id' => 'required|integer|exists:application_comments,id',
            'application_id' => ['required', 'exists:applications,id'],
        ];
    }
}
