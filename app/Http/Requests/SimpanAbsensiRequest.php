<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class SimpanAbsensiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Konversi foto_base64 jika dikirim sebagai string data URL menjadi UploadedFile.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->hasFile('foto') && $this->filled('foto_base64')) {
            $base64 = (string) $this->input('foto_base64');
            if (preg_match('/^data:image\/(jpeg|png|jpg);base64,/', $base64, $matches)) {
                $sub = substr($base64, strpos($base64, ',') + 1);
                $decoded = base64_decode($sub);
                if ($decoded !== false) {
                    $tmpPath = tempnam(sys_get_temp_dir(), 'abs_');
                    file_put_contents($tmpPath, $decoded);
                    $subExt = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];

                    $file = new UploadedFile(
                        $tmpPath,
                        'kamera_absen.'.$subExt,
                        'image/'.$matches[1],
                        null,
                        true
                    );

                    $this->files->set('foto', $file);
                }
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'foto' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'foto.required' => 'Foto absensi wajib diambil melalui kamera sebelum menyimpan.',
            'foto.file' => 'Data foto yang dikirimkan tidak valid.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto hanya diperbolehkan JPEG atau PNG.',
            'foto.max' => 'Ukuran file foto maksimal adalah 2 MB.',
        ];
    }
}
