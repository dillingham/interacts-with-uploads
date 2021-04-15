<?php

namespace Dillingham\InteractsWithUploads\Tests\Fixtures;

use Dillingham\InteractsWithUploads;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    use InteractsWithUploads;

    public function rules()
    {
        return [
            'title' => 'required',
            'body' => 'required',
            'image' => 'required',
        ];
    }
}
