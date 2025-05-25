<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'message_type' => 'required|in:internal,external',
            'recipients' => 'required|array|min:1',
            'recipients.*.type' => 'required|in:internal,external',
            'recipients.*.id' => 'required_if:recipients.*.type,internal|exists:users,id',
            'recipients.*.email' => 'required_if:recipients.*.type,external|email',
            'attachments.*' => 'sometimes|file|max:10240', 
        ];
    }
}
