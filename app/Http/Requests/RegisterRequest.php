<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'between:1,10', 'unique:users'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'between:6,30', 'confirmed'],
        ];
    }

    /**
     * 定義済みバリデーションルールのエラーメッセージ取得
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => '名前は必須項目です',
            'name.between' => '名前は:max文字以内で記述してください',
            'name.unique' => 'その名前は既に使用されています',
            'email.required' => 'メールアドレスは必須項目です',
            'email.email' => '無効なメールアドレスの形式です',
            'email.unique' => 'そのメールアドレスは既に使用されています',
            'password.required' => 'パスワードは必須項目です',
            'password.between' => 'パスワードは:min文字以上、:max文字以内で記述してください',
            'password.confirmed' => 'パスワードが一致しません',
        ];
    }
}
