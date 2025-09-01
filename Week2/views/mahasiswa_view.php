<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Mahasiswa</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 900px; margin: 24px auto; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background: #f5f5f5; text-align: left; }
    .error { color: red; font-style: italic; margin: 4px 0 8px; }
    form > div { margin-bottom: 12px; }
    input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; }
    button { padding: 8px 14px; cursor: pointer; }
  </style>
</head>
<body>
  <h1>Data Mahasiswa</h1>

  <table>
    <thead>
      <tr>
        <th>NIM</th>
        <th>Nama</th>
        <th>Jurusan</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($mahasiswa)) : ?>
        <?php foreach ($mahasiswa as $mhs) : ?>
          <tr>
            <td><?= htmlspecialchars($mhs['nim']) ?></td>
            <td><?= htmlspecialchars($mhs['nama']) ?></td>
            <td><?= htmlspecialchars($mhs['jurusan']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else : ?>
        <tr><td colspan="3" style="text-align:center;">Belum ada data.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <h2>Tambah Mahasiswa Baru</h2>
  <form method="POST">
    <div>
      <label>NIM</label>
      <input type="text" name="nim" value="<?= isset($_POST['nim']) ? htmlspecialchars($_POST['nim']) : '' ?>">
      <?php if (isset($errors['nim'])) : ?>
        <p class="error"><?= $errors['nim']; ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label>Nama</label>
      <input type="text" name="nama" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>">
      <?php if (isset($errors['nama'])) : ?>
        <p class="error"><?= $errors['nama']; ?></p>
      <?php endif; ?>
    </div>

    <div>
      <label>Jurusan</label>
      <input type="text" name="jurusan" value="<?= isset($_POST['jurusan']) ? htmlspecialchars($_POST['jurusan']) : '' ?>">
      <?php if (isset($errors['jurusan'])) : ?>
        <p class="error"><?= $errors['jurusan']; ?></p>
      <?php endif; ?>
    </div>

    <button type="submit">Simpan</button>
  </form>
</body>
</html>