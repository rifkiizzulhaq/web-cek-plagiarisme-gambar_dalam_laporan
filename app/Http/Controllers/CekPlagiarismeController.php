<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use App\Models\ImagePlagiarismReport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CekPlagiarismeController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = env('PYTHON_API_URL', 'http://localhost:5000');
    }

    public function CekPlagiarisme()
    {
        // Ambil daftar file milik pengguna yang sedang login
        $query = File::where('user_id', auth()->id());

        // Jika yang login adalah admin, tampilkan semua file
        if (optional(auth()->user())->hasRole('admin')) {
            $query = File::query();
        }

        // Urutkan dari yang terbaru dan gunakan paginasi
        $files = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Kirim data 'files' ke view
        return view('CekPlagiarisme', compact('files'));
    }

    public function upload(Request $request)
    {
        $path = null;
        try {
            $request->validate(['file' => 'required|mimes:docx|max:10240']);

            if (!$request->hasFile('file')) {
                return response()->json(['success' => false, 'message' => 'Tidak ada file yang diupload'], 400);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $fileName, 'public');
            $apiUrl = $this->apiUrl;

            $result = DB::transaction(function () use ($path, $fileName, $file, $apiUrl) {
                $fileModel = File::create([
                    'user_id' => auth()->id(),
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'extension' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'status' => 'processing'
                ]);

                $response = Http::attach(
                    'file', file_get_contents(Storage::disk('public')->path($path)), $fileName
                )->post($apiUrl . '/api/check-plagiarism');

                $responseData = $response->json();

                if (isset($responseData['success']) && $responseData['success'] === false) {
                    throw new \Exception($responseData['message'] ?? 'Terjadi kesalahan di server Python.');
                }
                if (!isset($responseData['data'])) {
                    throw new \Exception('Respons tidak valid dari server pemrosesan.');
                }
                
                $resultData = $responseData['data'];
                
                $fileModel->update([
                    'status' => 'completed',
                    'total_sentences' => $resultData['total_sentences'],
                    'plagiarized_sentences' => $resultData['plagiarized_sentences'],
                    'similarity_percentage' => $resultData['similarity_percentage'],
                    'total_images' => $resultData['total_images'],    
                    'indicated_images' => $resultData['indicated_images'], 
                ]);

                if (!empty($resultData['image_similarity_report'])) {
                    foreach ($resultData['image_similarity_report'] as $report) {
                        ImagePlagiarismReport::create([
                            'file_id' => $fileModel->id,
                            'source_image_index' => $report['source_image_index'],
                            'source_image' => $report['source_image'],
                            'match_image' => $report['match_image'],
                            'match_doc_title' => $report['match_doc_title'],
                            'similarity' => $report['similarity'],
                        ]);
                    }
                }

                return [
                    'success' => true,
                    'message' => 'File berhasil diproses',
                    'redirect' => route('view.document', ['file_id' => $fileModel->id])
                ];
            });

            return response()->json($result);

        } catch (\Throwable $e) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            Log::error('Upload error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function viewDocument($file_id)
    {
        $file = File::with('imagePlagiarismReports')->findOrFail($file_id);
        
        if (auth()->id() !== $file->user_id && !optional(auth()->user())->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses dokumen ini.');
        }
        
        if ($file->status !== 'completed') {
            return view('ViewDocument', ['file' => $file, 'error' => 'Dokumen masih dalam proses.']);
        }

        return view('ViewDocument', [
            'file' => $file,
            'image_plagiarism_report' => $file->imagePlagiarismReports
        ]);
    }

    public function getFileContent($file_id)
    {
        $file = File::findOrFail($file_id);

        // Otorisasi: Hanya pemilik file atau admin yang bisa melihat
        if (auth()->id() !== $file->user_id && !optional(auth()->user())->hasRole('admin')) {
            abort(403, 'Akses ditolak.');
        }

        $path = storage_path('app/public/' . $file->path);

        if (!\Illuminate\Support\Facades\File::exists($path)) {
            abort(404, 'File fisik tidak ditemukan di server.');
        }

        return response()->file($path);
    }
}