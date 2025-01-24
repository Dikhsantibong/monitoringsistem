<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HandleDocumentDownload
{
    protected $mimeTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log untuk debugging
        Log::info('Document request', [
            'path' => $request->path(),
            'content_type' => $response->headers->get('Content-Type')
        ]);

        if ($response->headers->has('Content-Type') && 
            $response->headers->get('Content-Type') === 'application/octet-stream') {
            
            $path = $request->path();
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            
            if (isset($this->mimeTypes[$extension])) {
                $response->headers->set('Content-Type', $this->mimeTypes[$extension]);
                
                // Set additional headers for better download handling
                if (in_array($extension, ['doc', 'docx', 'pdf'])) {
                    $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($path) . '"');
                }
            }
            
            // Log hasil perubahan
            Log::info('Modified response', [
                'extension' => $extension,
                'new_content_type' => $response->headers->get('Content-Type')
            ]);
        }

        return $response;
    }
} 