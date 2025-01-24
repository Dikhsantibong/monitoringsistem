<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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

        if ($response->headers->has('Content-Type') && $response->headers->get('Content-Type') === 'application/octet-stream') {
            $path = $request->path();
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            
            if (isset($this->mimeTypes[$extension])) {
                $response->headers->set('Content-Type', $this->mimeTypes[$extension]);
            }
        }

        return $response;
    }
} 