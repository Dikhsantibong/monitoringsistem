<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OtherDiscussion;
use Illuminate\Support\Facades\Storage;

class FixDocumentExtensions extends Command
{
    protected $signature = 'documents:fix-extensions';
    protected $description = 'Fix document extensions from .bin to their original extensions';

    public function handle()
    {
        $discussions = OtherDiscussion::whereNotNull('document_path')->get();

        foreach ($discussions as $discussion) {
            if (strpos($discussion->document_path, '.bin') !== false) {
                // Ambil nama asli dari document_description
                if ($discussion->document_description && 
                    preg_match('/\.(pdf|doc|docx|jpg|jpeg|png)$/i', $discussion->document_description, $matches)) {
                    
                    $newPath = str_replace('.bin', '.' . strtolower($matches[1]), $discussion->document_path);
                    
                    // Rename file
                    if (Storage::disk('public')->exists($discussion->document_path)) {
                        Storage::disk('public')->move($discussion->document_path, $newPath);
                        
                        // Update database
                        $discussion->update(['document_path' => $newPath]);
                        
                        $this->info("Fixed extension for document ID {$discussion->id}");
                    }
                }
            }
        }

        $this->info('Document extensions fix completed');
    }
} 