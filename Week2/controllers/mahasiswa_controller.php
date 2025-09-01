<?php
// Data statis sementara
$mahasiswa = [
    ["nim" => "2101001", "nama" => "Agus", "jurusan" => "Informatika"],
    ["nim" => "2101002", "nama" => "Ikhsan", "jurusan" => "Sistem Informasi"],
];

// Pastikan $errors selalu tersedia
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil nilai
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    // Validasi sederhana
    if ($nim === '') {
        $errors['nim'] = 'NIM wajib diisi.';
    }
    if ($nama === '') {
        $errors['nama'] = 'Nama wajib diisi.';
    }
    if ($jurusan === '') {
        $errors['jurusan'] = 'Jurusan wajib diisi.';
    }

    // Jika tidak ada error, tambahkan data ke array (simulasi)
    if (empty($errors)) {
        $mahasiswa[] = [
            'nim' => $nim,
            'nama' => $nama,
            'jurusan' => $jurusan,
        ];
    }
}

// Muat view (sesuaikan dengan nama file yang ada: mahasiswa_view.php)
require __DIR__ . '/../views/mahasiswa_view.php';