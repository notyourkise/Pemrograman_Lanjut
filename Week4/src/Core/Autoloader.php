<?php
// Simple PSR-4 like autoloader for namespace App\

spl_autoload_register(function($class){
    // Hanya proses class yang diawali 'App\\'
    $prefix = 'App\\';
    if (strpos($class, $prefix) !== 0) {
        return; // bukan milik namespace kita
    }
    // Ubah namespace menjadi path relatif
    $relative = substr($class, strlen($prefix)); // e.g. Models\Mahasiswa
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

    $baseDir = __DIR__ . '/../'; // src/Core -> src/
    $file = realpath($baseDir . $relativePath) ?: $baseDir . $relativePath;

    if (file_exists($file)) {
        require $file;
    } else {
        // Optional: echo atau log untuk debugging
        // echo "Autoloader: file tidak ditemukan: $file"; 
    }
});
