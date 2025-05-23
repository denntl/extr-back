<?php

namespace App\Http\Requests\Admin\Manage\Application;

use App\Enums\Application\Status;
use App\Rules\InEnum;
use App\Services\Manage\Application\ApplicationService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveApplicationRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'id' => 'int|exists:applications,id',
            'platform_type' => 'nullable|integer',
            'landing_type' => 'nullable|integer',
            'category' => 'nullable|integer',
            'white_type' => 'nullable|integer',
            'name' => 'required|string|min:5|max:255',
            'domain_id' => 'required|integer|exists:domains,id',
            'subdomain' => ['required','string','max:255'],
            'pixel_id' => 'required|string|max:255',
            'pixel_key' => 'required|string|max:255',
            'link' => 'required|string',
            'status' => ['required', 'integer', new InEnum(Status::class)],
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
            'downloads_count' => 'nullable|string|max:20',
            'rating' => 'nullable|numeric|min:0|max:5',
            'app_name' => 'nullable|string|max:255',
            'developer_name' => 'nullable|string|max:255',
            'onesignal_auth_key' => 'required|string|max:255',
            'files' => 'nullable|array',
            'files.*.id' => 'integer|exists:files,id',
            'owner_id' => 'sometimes|integer|exists:users,id',
            'topApplicationIds' => 'array|max:10',
            'topApplicationIds.*' => 'nullable|integer',
            'applicationGeoLanguages' => 'array|min:1',
            'applicationGeoLanguages.*.geo' => 'string|max:3|required',
            'applicationGeoLanguages.*.language' => 'string|max:3|required',
        ];


        $domainId = $this->request->get('domain_id');
        $appId = $this->request->get('id');
        if ($appId) {
            $rules['subdomain'][] = Rule::unique('applications', 'subdomain')->ignore($appId)->where('domain_id', $domainId);
        } else {
            $rules['subdomain'][] = Rule::unique('applications', 'subdomain')->where('domain_id', $domainId);
        }

        return $rules;
    }


    public function messages()
    {
        return [
            'subdomain.unique' => __('client.application.subdomain_unique_error'),
        ];
    }
}
