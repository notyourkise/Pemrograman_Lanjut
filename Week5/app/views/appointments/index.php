<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="breadcrumb">Beranda / Janji Temu</div>

<div class="card">
  <div style="display:flex; gap:10px; justify-content:space-between; align-items:center;">
    <form method="get" action="" class="form-inline">
      <input type="hidden" name="c" value="appointments">
      <input type="hidden" name="a" value="index">
      <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama pasien atau catatan" class="input">
      <button class="btn" type="submit">Cari</button>
    </form>
    <a class="btn" href="?c=appointments&a=create">+ Tambah Janji</a>
  </div>
  <div style="margin-top:10px;">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th><a href="?c=appointments&a=index&sort=p.name&dir=<?= $dir==='ASC'?'DESC':'ASC' ?>&q=<?= urlencode($q) ?>">Pasien</a></th>
          <th><a href="?c=appointments&a=index&sort=a.schedule&dir=<?= $dir==='ASC'?'DESC':'ASC' ?>&q=<?= urlencode($q) ?>">Jadwal</a></th>
          <th>Catatan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($appointments as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['patient_name'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['schedule']) ?></td>
            <td><?= htmlspecialchars($r['notes'] ?? '') ?></td>
            <td>
              <a class="btn btn-outline" href="?c=appointments&a=edit&id=<?= (int)$r['id'] ?>">Edit</a>
              <button class="btn btn-danger" onclick="confirmDelete(<?= (int)$r['id'] ?>)">Hapus</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <?php $pages = (int)ceil($total / $perPage); if ($pages > 1): ?>
      <div class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <a class="btn <?= $i === $page ? '' : 'btn-outline' ?>" href="?c=appointments&a=index&page=<?= $i ?>&q=<?= urlencode($q) ?>&sort=<?= urlencode($sort) ?>&dir=<?= urlencode($dir) ?>"><?= $i ?></a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="modal" id="deleteModal" style="display:none;">
  <div class="modal-content">
    <h3>Konfirmasi Hapus</h3>
    <p>Yakin ingin menghapus janji temu ini?</p>
    <form method="post" id="deleteForm">
      <button class="btn btn-danger" type="submit">Hapus</button>
      <button class="btn btn-outline" type="button" onclick="closeModal()">Batal</button>
    </form>
  </div>
</div>
<script>
  function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = `?c=appointments&a=delete&id=${id}`;
    document.getElementById('deleteModal').style.display = 'block';
  }
  function closeModal() { document.getElementById('deleteModal').style.display = 'none'; }
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
