<?php
namespace App\Traits;

trait CanIntroduce {
    public function introduce(): string {
        return 'Halo, saya ' . ($this->nama ?? 'Anonim');
    }
}