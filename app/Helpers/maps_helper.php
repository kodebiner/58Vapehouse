<?php

// Normalisasi nilai maps outlet.
//
// Kolom `outlet.maps` menyimpan nilai param `pb` Google Maps Embed.
// Input admin boleh berupa full <iframe>, full URL embed, atau
// potongan `pb` mentah (format lama) — semuanya dinormalisasi
// menjadi nilai `pb` sebelum disimpan.

if (!function_exists('extract_maps_pb')) {
    /**
     * Extract the `pb` param value from a Google Maps embed input.
     */
    function extract_maps_pb(?string $input): string
    {
        $input = trim((string) $input);
        if ($input === '') {
            return '';
        }

        // 1. Full iframe code pasted from Google Maps "Embed a map" -> take src="..."
        if (preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $input, $m)) {
            $input = trim($m[1]);
        }

        // Normalize HTML-escaped ampersands before parsing
        $input = str_replace('&amp;', '&', $input);

        // 2. Full URL -> take the pb query param (raw, keep %3A encoding intact)
        if (preg_match('/[?&]pb=([^&"\']+?)(["\'\s]|$)/', $input . ' ', $m)) {
            return trim($m[1]);
        }

        // 3. Fallback: already a bare pb fragment, strip stray quotes/spaces
        return trim($input, "\"' ");
    }
}

if (!function_exists('maps_embed_src')) {
    /**
     * Build the full iframe src URL from a stored `maps` value.
     *
     * Legacy rows were stored without the trailing params that the
     * web layout used to append — keep appending them so old
     * outlets keep rendering exactly as before.
     */
    function maps_embed_src($stored): string
    {
        $pb = extract_maps_pb(isset($stored) ? (string) $stored : '');
        if ($pb === '') {
            return '';
        }

        if (strpos($pb, '!5e0') === false && strpos($pb, '!5m2') === false) {
            $pb .= '!5e0!3m2!1sen!2sid!4v1695478742306!5m2!1sen!2sid';
        }

        return 'https://www.google.com/maps/embed?pb=' . $pb;
    }
}
