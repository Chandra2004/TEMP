<?php

namespace TheFramework\Http\Requests;

use TheFramework\App\FormRequest;

/**
 * Request validation untuk EventRequest
 * 
 * Auto-validates when used in controller!
 * No need to call validate() manually.
 * 
 * Usage:
 * public function store(EventRequest $request) {
 *     // Validation already done automatically
 *     // If we reach here, validation passed
 *     Model::create($request->validated());
 * }
 */
class EventRequest extends FormRequest
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
        return [
            'banner_event' => 'nullable|mimes:jpg,png,jpeg|max:2048',
            'logo_kiri' => 'nullable|mimes:jpg,png,jpeg|max:2048',
            'logo_kanan' => 'nullable|mimes:jpg,png,jpeg|max:2048',
            'nama_event' => 'required',
            'deskripsi' => 'nullable|string',
            'lokasi_event' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'waktu_mulai' => 'required',
            'jumlah_lintasan' => 'required',
            'status_event' => 'required',
            'uid_author' => 'required',
            'uid_payment_method' => 'nullable',
            // Simple array validation (manual validation will be done in Controller)
            'matches' => 'required|array|min:1',
        ];
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
            'banner_event' => 'banner event',
            'logo_kiri' => 'logo kiri',
            'logo_kanan' => 'logo kanan',
            'nama_event' => 'nama event',
            'deskripsi' => 'deskripsi',
            'lokasi_event' => 'lokasi event',
            'waktu_event' => 'waktu event',
            'tanggal_event' => 'tanggal event',
            'biaya_event' => 'biaya event',
            'status_event' => 'status event',
            'kuota_peserta' => 'kuota peserta',
            'tipe_event' => 'tipe event',
            'matches.*.uid_category' => 'nomor lomba',
            'matches.*.nama_acara' => 'nama tampilan lomba',
            'matches.*.biaya_pendaftaran' => 'biaya pendaftaran',
            'matches.*.tipe_biaya' => 'tipe biaya',
            'matches.*.waktu_mulai' => 'waktu mulai lomba',
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