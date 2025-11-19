<?php
require __DIR__ . '/Core/Autoloader.php';

use App\Models\Mahasiswa;
use App\Models\Person; // for static counter

$students = [
    new Mahasiswa('1','haikal','10231080','Informatika'),
    new Mahasiswa('2','aril','10231081','Sistem Informasi'),
    new Mahasiswa('3','irpan','10231082','teknik lantai')
];

?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Week4 - OOP Lanjutan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        table { border-collapse: collapse; }
        th, td { border:1px solid #ccc; padding:6px 10px; }
    </style>
</head>
<body>
<h2>Demo OOP Lanjutan</h2>
<p>Total Instance Person: <?= Person::getJumlah(); ?></p>
<table>
    <thead>
    <tr>
        <th>ID</th><th>NIM</th><th>Nama</th><th>Role</th><th>Introduce()</th><th>Deskripsi</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($students as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s->getId()) ?></td>
            <td><?= htmlspecialchars(substr($s->deskripsi(),0,8)) ?></td>
            <td><?= htmlspecialchars($s->getNama()) ?></td>
            <td><?= htmlspecialchars($s->getRole()) ?></td>
            <td><?= htmlspecialchars($s->introduce()) ?></td>
            <td><?= htmlspecialchars($s->deskripsi()) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<p>Static Counter (Person::getJumlah()): <?= Person::getJumlah(); ?></p>
</body>
</html>