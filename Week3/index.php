<?php
// Week 3 - OOP Dasar Demo
// Kerjakan bertahap: setiap blok bisa Anda hapus/comment setelah paham.

// 1. Class sederhana + instansiasi manual properti
class MahasiswaA {
    public $nim;
    public $nama;
    public $jurusan;
}
$mA = new MahasiswaA();
$mA->nim = '10231063';
$mA->nama = 'Haikal';
$mA->jurusan = 'Sistem Informasi';

// 2. Versi dengan constructor + method deskripsi
class Mahasiswa {
    public $nim;
    public $nama;
    public $jurusan;

    public function __construct($nim, $nama, $jurusan) {
        $this->nim = $nim;
        $this->nama = $nama;
        $this->jurusan = $jurusan;
    }

    public function deskripsi() {
        return "{$this->nim} - {$this->nama} ({$this->jurusan})";
    }
}

$m1 = new Mahasiswa('10231063','Haikal','Sistem Informasi');
$m2 = new Mahasiswa('10231073','Pangeran','Sistem Informasi');

// 3. Versi dengan properti private + getter
class MahasiswaPriv {
    private $nim;
    private $nama;
    private $jurusan;

    public function __construct($nim, $nama, $jurusan) {
        $this->nim = $nim;
        $this->nama = $nama;
        $this->jurusan = $jurusan;
    }
    public function getNama() { return $this->nama; }
    public function deskripsi() { return "{$this->nim} - {$this->nama} ({$this->jurusan})"; }
}

$mP = new MahasiswaPriv('10231073','Pangeran','Sistem Informasi');

// 4. Array of objects
$daftar = [ $m1, $m2 ];

// 5. (Opsional) Form tambah mahasiswa (in-memory)
class MahasiswaForm {
    private $nim; private $nama; private $jurusan;
    public function __construct($nim,$nama,$jurusan){$this->nim=$nim;$this->nama=$nama;$this->jurusan=$jurusan;}
    public function deskripsi(){return "{$this->nim} - {$this->nama} ({$this->jurusan})";}
}

$listForm = [ new MahasiswaForm('10231063','Haikal','Sistem Informasi'), new MahasiswaForm('10231073','Pangeran','Sistem Informasi') ];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    if ($nim==='') $errors['nim'] = 'NIM wajib';
    if ($nama==='') $errors['nama'] = 'Nama wajib';
    if ($jurusan==='') $errors['jurusan'] = 'Jurusan wajib';
    if (empty($errors)) {
        $listForm[] = new MahasiswaForm($nim,$nama,$jurusan);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Week3 - OOP Dasar</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        .box { padding:12px; border:1px solid #ccc; margin-bottom:18px; }
        .error { color:#b00; font-size:12px; }
        input { margin-bottom:6px; }
    </style>
</head>
<body>
<h2>Demo OOP Dasar</h2>
<div class="box">
    <h3>1. Instansiasi Manual Properti</h3>
    <p><?= htmlspecialchars($mA->nim) ?> - <?= htmlspecialchars($mA->nama) ?> (<?= htmlspecialchars($mA->jurusan) ?>)</p>
</div>
<div class="box">
    <h3>2. Constructor + Method</h3>
    <p><?= htmlspecialchars($m1->deskripsi()) ?><br><?= htmlspecialchars($m2->deskripsi()) ?></p>
</div>
<div class="box">
    <h3>3. Private + Getter</h3>
    <p>Nama via getter: <?= htmlspecialchars($mP->getNama()) ?></p>
    <p>Deskripsi: <?= htmlspecialchars($mP->deskripsi()) ?></p>
</div>
<div class="box">
    <h3>4. Array of Objects</h3>
    <?php foreach ($daftar as $m): ?>
        <?= htmlspecialchars($m->deskripsi()) ?><br>
    <?php endforeach; ?>
</div>
<div class="box">
    <h3>5. Form (Opsional)</h3>
    <form method="POST">
        <div>
            <label>NIM: <input name="nim" value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>"></label>
            <?php if(isset($errors['nim'])): ?><span class="error"><?= $errors['nim'] ?></span><?php endif; ?>
        </div>
        <div>
            <label>Nama: <input name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"></label>
            <?php if(isset($errors['nama'])): ?><span class="error"><?= $errors['nama'] ?></span><?php endif; ?>
        </div>
        <div>
            <label>Jurusan: <input name="jurusan" value="<?= htmlspecialchars($_POST['jurusan'] ?? '') ?>"></label>
            <?php if(isset($errors['jurusan'])): ?><span class="error"><?= $errors['jurusan'] ?></span><?php endif; ?>
        </div>
        <button type="submit">Tambah</button>
    </form>
    <h4>Data (In-Memory)</h4>
    <?php foreach ($listForm as $m): ?>
        <?= htmlspecialchars($m->deskripsi()) ?><br>
    <?php endforeach; ?>
</div>
</body>
</html>
