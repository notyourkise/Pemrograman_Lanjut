<?php
namespace App\Models;

class Mahasiswa extends Person {
    private string $nim;
    private string $jurusan;

    public function __construct(string $id, string $nama, string $nim, string $jurusan) {
        parent::__construct($id, $nama);
        $this->nim = $nim;
        $this->jurusan = $jurusan;
    }

    public function getRole(): string { return 'Mahasiswa'; }

    public function deskripsi(): string {
        return $this->nim . ' - ' . $this->getNama() . ' (' . $this->jurusan . ')';
    }
}