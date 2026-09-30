<?php

namespace App\Helpers;

class EmbedHelper
{
    /**
     * Konversi URL biasa menjadi URL Embed resmi untuk iFrame
     */
    public static function formatToEmbedUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $url = trim($url);

        // 1. YouTube (Watch, Shorts, Share Link)
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
            if (isset($matches[1])) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        // 2. Google Slides
        if (str_contains($url, 'docs.google.com/presentation')) {
            return preg_replace('/\/(edit|pub)(.*)?$/', '/embed', $url);
        }

        // 3. Google Docs / Google Sheets / Google Form
        if (str_contains($url, 'docs.google.com')) {
            if (str_contains($url, '/edit')) {
                return preg_replace('/\/edit(.*)?$/', '/preview', $url);
            }
            if (!str_contains($url, '/preview') && !str_contains($url, '/pubhtml')) {
                return rtrim($url, '/') . '/preview';
            }
            return $url;
        }

        // 4. Google Drive (File Viewer / PDF / Video di Drive)
        if (str_contains($url, 'drive.google.com')) {
            if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return 'https://drive.google.com/file/d/' . $matches[1] . '/preview';
            }
        }

        // 5. Canva (Desain / Presentasi)
        if (str_contains($url, 'canva.com')) {
            if (preg_match('/\/design\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                return 'https://www.canva.com/design/' . $matches[1] . '/watch?embed';
            }
        }

        return $url;
    }
}