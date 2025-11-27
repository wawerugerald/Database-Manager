<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManageDatabaseRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'id' => 'required|integer|exists:database_instances,id',
        ];
    }
}
