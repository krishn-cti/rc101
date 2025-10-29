<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class CmsHelper
{
    public static function cleanupCkeditorImages($content)
    {
        $uploadPath = public_path('uploads/ckeditor');

        if (!File::exists($uploadPath)) {
            return;
        }

        // Get all images in the folder
        $allImages = File::files($uploadPath);

        // Extract image filenames used in the content
        preg_match_all('/src="[^"]*\/uploads\/ckeditor\/([^"]+)"/', $content, $matches);
        $usedImages = $matches[1] ?? [];

        // Loop through all files and delete unused ones
        foreach ($allImages as $image) {
            $filename = $image->getFilename();

            if (!in_array($filename, $usedImages)) {
                File::delete($image->getRealPath());
            }
        }
    }
}