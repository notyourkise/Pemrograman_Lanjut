<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="breadcrumb">Beranda / Janji Temu / Tambah</div>

<div class="card">
  <form method="post">
    <div class="form-group">
      <label>Pasien</label>
      <select name="patient_id" class="input">
        <option value="">-- Pilih Pasien --</option>
        <?php foreach ($patients as $p): ?>
          <option value="<?= (int)$p['id'] ?>" <?= ((int)$old['patient_id'] === (int)$p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if (!empty($errors['patient_id'])): ?><div class="error"><?= htmlspecialchars($errors['patient_id']) ?></div><?php endif; ?>
    </div>
    <div class="form-group">
      <label>Jadwal</label>
      <input class="input" type="datetime-local" name="schedule" value="<?= htmlspecialchars($old['schedule']) ?>" placeholder="YYYY-MM-DDTHH:MM">
      <?php if (!empty($errors['schedule'])): ?><div class="error"><?= htmlspecialchars($errors['schedule']) ?></div><?php endif; ?>
    </div>
    <div class="form-group">
      <label>Catatan</label>
      <textarea class="input" name="notes" rows="3" placeholder="Opsional..."><?= htmlspecialchars($old['notes']) ?></textarea>
    </div>
    <div class="form-actions">
      <button class="btn" type="submit">Simpan</button>
      <a class="btn btn-outline" href="?c=appointments&a=index">Batal</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
