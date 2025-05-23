<?php

namespace App\Http\Requests\Admin\Manage\Company;

use App\Enums\Balance\Type;
use App\Rules\InEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ManualBalanceDepositRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'between:0.01,1000'],
            'comment' => ['required', 'string', 'max:255'],
            'balanceType' => ['required', 'int', new InEnum(Type::class)],
        ];
    }
}
