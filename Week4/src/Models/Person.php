<?php
namespace App\Models;

use App\Interfaces\HasIdentity;
use App\Traits\CanIntroduce;

abstract class Person implements HasIdentity {
    use CanIntroduce;

    protected string $nama;
    protected string $id;
    protected static int $jumlah = 0;

    public function __construct(string $id, string $nama) {
        $this->id = $id;
        $this->nama = $nama;
        static::$jumlah++;
    }

    abstract public function getRole(): string;

    public function getId(): string { return $this->id; }
    public function getNama(): string { return $this->nama; }

    public static function getJumlah(): int { return static::$jumlah; }
}