<?php $title = 'Edit Pasien - Week6'; require __DIR__ . '/../layout/header.php'; ?>
<div class="stack">
  <div class="card">
    <div class="breadcrumb"><a href="/MATERI-ASDOS/Week6/public/">Home</a><span class="sep">/</span><a href="?c=patients&a=index">Pasien</a><span class="sep">/</span><span>Edit</span></div>
    <h2 style="margin:0;">Edit Pasien #<?= (int)$id ?></h2>
    <div class="muted">Perbarui data pasien</div>
  </div>
  <div class="card">
    <form class="grid cols-2" method="post">
      <div class="field" style="grid-column: 1 / -1;">
        <label>Nama</label>
        <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <?php if (!empty($errors['name'])): ?><div class="error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
      </div>
      <div class="field">
        <label>Gender</label>
        <select name="gender">
          <option value="M" <?= (($old['gender'] ?? 'M') === 'M') ? 'selected' : '' ?>>Laki-Laki</option>
          <option value="F" <?= (($old['gender'] ?? 'M') === 'F') ? 'selected' : '' ?>>Perempuan</option>
        </select>
      </div>
      <div class="field">
        <label>Tgl Lahir</label>
        <?php $today = date('Y-m-d'); $dobVal = isset($old['dob']) && $old['dob'] ? $old['dob'] : ''; ?>
        <input type="date" name="dob" max="<?= $today ?>" placeholder="Pilih tanggal" value="<?= htmlspecialchars($dobVal, ENT_QUOTES, 'UTF-8') ?>">
        <?php if (!empty($errors['dob'])): ?><div class="error"><?= htmlspecialchars($errors['dob'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
      </div>
      <div class="field">
        <label>Telepon</label>
        <input type="text" name="phone" value="<?= htmlspecialchars((string)($old['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <?php if (!empty($errors['phone'])): ?><div class="error"><?= htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
      </div>
      <div class="field" style="grid-column: 1 / -1;">
        <label>Alamat</label>
        <textarea name="address" rows="3"><?= htmlspecialchars((string)($old['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <div style="grid-column: 1 / -1; display:flex; gap:10px;">
        <button class="btn" type="submit">Simpan</button>
        <a class="btn btn-outline" href="?c=patients&a=index">Batal</a>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>