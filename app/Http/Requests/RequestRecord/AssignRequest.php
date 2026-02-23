<?php

namespace App\Http\Requests\RequestRecord;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role?->name === 'dispatcher';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $masterRole = Role::query()->where('name', 'master')->first();
        
        return [
            'master_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role_id', $masterRole?->id),
            ],
        ];
    }
}
