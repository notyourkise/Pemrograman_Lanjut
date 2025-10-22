<?php
/**
 * Input/Output Sanitization Helper
 * 
 * Helper untuk membersihkan input dan escape output
 * untuk mencegah XSS dan injection attacks
 */
class Sanitizer
{
    /**
     * Bersihkan string input (trim + htmlspecialchars)
     * 
     * @param string $input String yang akan dibersihkan
     * @return string String yang sudah bersih
     */
    public static function clean(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Bersihkan array input
     * 
     * @param array $data Array yang akan dibersihkan
     * @return array Array yang sudah bersih
     */
    public static function cleanArray(array $data): array
    {
        $cleaned = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $cleaned[$key] = self::cleanArray($value);
            } elseif (is_string($value)) {
                $cleaned[$key] = self::clean($value);
            } else {
                $cleaned[$key] = $value;
            }
        }
        return $cleaned;
    }
    
    /**
     * Escape output untuk ditampilkan di HTML
     * Alias untuk clean() agar lebih jelas maksudnya
     * 
     * @param mixed $output Data yang akan di-escape
     * @return string Output yang aman untuk HTML
     */
    public static function escape($output): string
    {
        if (is_null($output)) {
            return '';
        }
        return htmlspecialchars((string)$output, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Trim whitespace dari input
     * 
     * @param string $input String yang akan di-trim
     * @return string String hasil trim
     */
    public static function trim(string $input): string
    {
        return trim($input);
    }
    
    /**
     * Strip HTML tags dari input
     * 
     * @param string $input String yang akan dibersihkan
     * @param string|null $allowedTags Tag yang diizinkan (format: '<p><a>')
     * @return string String tanpa HTML tags
     */
    public static function stripTags(string $input, ?string $allowedTags = null): string
    {
        return strip_tags($input, $allowedTags);
    }
    
    /**
     * Sanitasi untuk input numerik
     * 
     * @param mixed $input Input yang akan disanitasi
     * @return int|float|null Nilai numerik atau null
     */
    public static function number($input)
    {
        if (is_null($input) || $input === '') {
            return null;
        }
        
        if (is_numeric($input)) {
            return strpos($input, '.') !== false ? (float)$input : (int)$input;
        }
        
        return null;
    }
    
    /**
     * Sanitasi untuk input integer
     * 
     * @param mixed $input Input yang akan disanitasi
     * @return int Nilai integer (0 jika invalid)
     */
    public static function int($input): int
    {
        return (int)filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Sanitasi untuk email
     * 
     * @param string $input Email yang akan disanitasi
     * @return string Email yang sudah disanitasi
     */
    public static function email(string $input): string
    {
        return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Sanitasi untuk URL
     * 
     * @param string $input URL yang akan disanitasi
     * @return string URL yang sudah disanitasi
     */
    public static function url(string $input): string
    {
        return filter_var(trim($input), FILTER_SANITIZE_URL);
    }
    
    /**
     * Sanitasi untuk nama file
     * 
     * @param string $filename Nama file yang akan disanitasi
     * @return string Nama file yang aman
     */
    public static function filename(string $filename): string
    {
        // Hapus karakter berbahaya
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        // Hapus multiple dots
        $filename = preg_replace('/\.+/', '.', $filename);
        return $filename;
    }
    
    /**
     * Sanitasi untuk SQL LIKE query (escape wildcard)
     * 
     * @param string $input Input untuk LIKE query
     * @return string String yang sudah di-escape
     */
    public static function escapeLike(string $input): string
    {
        return addcslashes($input, '%_');
    }
    
    /**
     * Konversi newline ke <br> untuk output HTML
     * 
     * @param string $text Text dengan newline
     * @return string Text dengan <br>
     */
    public static function nl2br(string $text): string
    {
        return nl2br(self::escape($text));
    }
    
    /**
     * Sanitasi input array POST/GET secara batch
     * 
     * @param array $inputs Array input ($_POST atau $_GET)
     * @param array $fields Field yang akan diambil
     * @return array Array hasil sanitasi
     */
    public static function inputs(array $inputs, array $fields): array
    {
        $result = [];
        foreach ($fields as $field) {
            $result[$field] = isset($inputs[$field]) ? trim($inputs[$field]) : '';
        }
        return $result;
    }
    
    /**
     * Sanitasi untuk boolean value
     * 
     * @param mixed $input Input yang akan dikonversi ke boolean
     * @return bool Nilai boolean
     */
    public static function bool($input): bool
    {
        return filter_var($input, FILTER_VALIDATE_BOOLEAN);
    }
}
