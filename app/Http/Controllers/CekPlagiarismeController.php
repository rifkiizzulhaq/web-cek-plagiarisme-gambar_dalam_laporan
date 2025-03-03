<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CekPlagiarismeController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('PYTHON_API_URL', 'http://localhost:5000');
    }

    public function CekPlagiarisme()
    {
        return view('CekPlagiarisme');
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:docx'
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Simpan file ke storage
                $path = $file->storeAs('uploads', $fileName, 'public');

                // Simpan informasi file ke database
                $fileModel = File::create([
                    'user_id' => auth()->id(),
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'extension' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'status' => 'processing'
                ]);

                // Kirim file ke Python API
                $response = Http::attach(
                    'file',
                    file_get_contents(Storage::disk('public')->path($path)),
                    $fileName
                )->post($this->apiUrl . '/api/check-plagiarism');

                if (!$response->successful()) {
                    throw new \Exception('Gagal memproses dokumen: ' . $response->body());
                }

                $result = $response->json();

                // Update status file
                $fileModel->update([
                    'status' => 'completed',
                    'result' => $result['data']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil diproses',
                    'redirect' => route('view.document', ['file_id' => $fileModel->id])
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewDocument($file_id)
    {
        try {
            $file = File::findOrFail($file_id);
            
            if ($file->status !== 'completed') {
                return view('ViewDocument', [
                    'file' => $file,
                    'error' => 'Dokumen masih dalam proses'
                ]);
            }

            // Ambil laporan dari Python API
            $response = Http::get($this->apiUrl . '/api/reports/plagiarism_report_' . $file->result['timestamp'] . '.html');
            
            if (!$response->successful()) {
                throw new \Exception('Gagal mengambil laporan');
            }

            return view('ViewDocument', [
                'file' => $file,
                'report_content' => $response->body(),
                'statistics' => [
                    'total_sentences' => $file->result['total_sentences'],
                    'plagiarized_sentences' => $file->result['plagiarized_sentences'],
                    'similarity_percentage' => $file->result['similarity_percentage']
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('View document error: ' . $e->getMessage());
            
            // Cek apakah $file sudah didefinisikan
            $fileData = isset($file) ? $file : null;
            
            return view('ViewDocument', [
                'file' => $fileData,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
