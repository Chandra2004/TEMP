<?php

namespace TheFramework\Http\Requests;

use TheFramework\App\FormRequest;
use TheFramework\Helpers\Helper;

/**
 * Request validation untuk RegistrationRequest
 * 
 * Auto-validates when used in controller!
 * No need to call validate() manually.
 * 
 * Usage:
 * public function store(RegistrationRequest $request) {
 *     // Validation already done automatically
 *     // If we reach here, validation passed
 *     Model::create($request->validated());
 * }
 */
class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Return false to deny access (403 Forbidden).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Available rules: required, email, min, max, unique, exists,
     * alpha, numeric, confirmed, in, between, mimes, image, etc.
     * 
     * @return array
     */
    public function rules(): array
    {
        $rules = [];
        if (Helper::getUriSegment(3) === 'create') {
            $rules = [
                'uid_user' => 'required|min:36',
                'uid_event' => 'required|min:36',
                'uid_event_category' => 'required',
                'metode_pembayaran' => 'nullable',
                'total_bayar' => 'nullable',
                'bukti_pembayaran' => 'nullable|mimes:jpg,png,jpeg|max:2048'
            ];
        } else if (Helper::getUriSegment(6) === 'edit') {
            $rules = [
                'status_pembayaran' => 'nullable',
                'catatan_admin' => 'nullable',
                'status_pendaftaran' => 'nullable',

            ];
        }

        return $rules;
    }

    /**
     * Get custom labels for validation error messages.
     * Makes error messages more user-friendly in your language.
     * 
     * @return array
     */
    public function labels(): array
    {
        return [
            // Example: 'email' => 'Alamat Email',
            // Example: 'password' => 'Kata Sandi',
        ];
    }

    /**
     * (OPTIONAL) Override this method to force JSON response on validation errors.
     * Uncomment the code below to always return JSON (useful for API endpoints).
     * 
     * @param array $errors
     * @return void
     */
    // protected function failedValidation(array $errors): void
    // {
    //     \TheFramework\Helpers\Helper::json([
    //         'status' => 'error',
    //         'errors' => $errors,
    //         'input' => $this->all()
    //     ], 422);
    //     exit;
    // }
}