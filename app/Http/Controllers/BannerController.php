<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;

class BannerController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        // Ambil data banner
        // Gunakan endpoint Admin yang mengembalikan SEMUA banner (aktif & nonaktif)
        $response = $this->api->get('/admin/banner');

        /** @var Response $response */
        
        // Pastikan data tidak null (jika API mengembalikan "data": null, ubah jadi array kosong)
        $banners = ($response && $response->successful()) ? ($response->json('data') ?? []) : [];
        return view('banner.index', compact('banners'));
    }

    public function create()
    {
        return view('banner.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:10240', // Max 10MB to allow upload before compression
        ]);

        $file = $request->file('foto');
        
        // Cek jika file > 2MB (2 * 1024 * 1024 bytes)
        if ($file->getSize() > 2 * 1024 * 1024) {
            try {
                $compressedFilePath = $this->compressImage($file);
                // Buat instance UploadedFile baru dari file yang sudah dikompres
                $file = new \Illuminate\Http\UploadedFile(
                    $compressedFilePath,
                    $file->getClientOriginalName(),
                    $file->getClientMimeType(),
                    null,
                    true
                );
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal mengompres gambar: ' . $e->getMessage())->withInput();
            }
        }

        $response = $this->api->postMultipart('/admin/banner', [
            'title' => $request->title
        ], 'foto', $file);

        /** @var Response $response */

        if ($response->successful()) {
            return redirect()->route('banner.index')->with('success', 'Banner berhasil dibuat');
        }

        return back()->withErrors($response->json('error') ?? 'Gagal upload banner')->withInput();
    }

    /**
     * Compress image to be under 2MB
     */
    private function compressImage($file)
    {
        $sourcePath = $file->getRealPath();
        $destinationPath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
        $quality = 90;
        $maxSize = 2 * 1024 * 1024; // 2MB

        // Get image info
        $imageInfo = getimagesize($sourcePath);
        $mime = $imageInfo['mime'];

        // Create image resource based on mime type
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                // Convert to JPG for better compression if needed, or keep PNG
                // For banner usually JPG is fine. Let's convert to JPG for consistent compression control
                // Handle transparency if needed
                $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagealphablending($bg, true);
                imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                imagedestroy($image);
                $image = $bg;
                break;
            default:
                throw new \Exception("Unsupported image type for compression");
        }

        // Loop to reduce quality until size is under limit
        do {
            // Save to destination
            imagejpeg($image, $destinationPath, $quality);
            
            // Allow garbage collection
            clearstatcache();
            $size = filesize($destinationPath);

            // Reduce quality for next iteration
            $quality -= 10;
        } while ($size > $maxSize && $quality > 10);

        imagedestroy($image);

        return $destinationPath;
    }

    public function toggle($id)
    {
        $response = $this->api->put("/admin/banner/{$id}/toggle");

        /** @var Response $response */

        if ($response && $response->successful()) {
            return redirect()->route('banner.index')->with('success', 'Status banner berhasil diubah');
        }

        return back()->with('error', 'Gagal mengubah status banner');
    }
}