<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|max:255',
        ];
    }

    public function attributes()
    {
        return[
            'name' => '名称',
        ];
    }

    protected function getRedirectUrl()
    {
        if (request()->routeIs('*update')){
            $url = $this->redirector->getUrlGenerator();
            return $url->route('admin.jobs.edit', ['job'=>request()->route()->parameter('job')]);
        }
        return parent::getRedirectUrl();

    }

}