<?php

if (! function_exists('storage_media_url')) {
    function storage_media_url(?string $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $normalizedPath = ltrim(str_replace('\\', '/', trim($path)), '/');

        if (str_starts_with($normalizedPath, 'http://') || str_starts_with($normalizedPath, 'https://')) {
            return $normalizedPath;
        }

        $supabaseUrl = rtrim((string) (config('services.supabase.url') ?? env('SUPABASE_URL', '')), '/');
        if ($supabaseUrl === '') {
            $supabaseUrl = 'https://humyyxoupzrupvggychb.supabase.co';
        }

        $bucket = 'school-logos';
        if (
            str_starts_with($normalizedPath, 'students-photos/') ||
            str_starts_with($normalizedPath, 'organization/') ||
            str_starts_with($normalizedPath, 'teachers/') ||
            str_starts_with($normalizedPath, 'teaching_evidence/') ||
            str_starts_with($normalizedPath, 'permission_letters/')
        ) {
            $bucket = 'school-private-media';
        }

        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $normalizedPath)));

        return $supabaseUrl.'/storage/v1/object/public/'.$bucket.'/'.$encodedPath;
    }
}
