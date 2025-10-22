<?php $title = 'Recycle Bin - Pasien - Week7'; require __DIR__ . '/../layout/header.php'; ?>
<div class="stack">
  <div class="card">
    <div class="breadcrumb">
      <a href="/MATERI-ASDOS/Week7/public/">Beranda</a><span class="sep">/</span>
      <a href="?c=patients&a=index">Pasien</a><span class="sep">/</span>
      <span>Recycle Bin</span>
    </div>
    <div style="display:flex; justify-content: space-between; align-items:center; gap:12px;">
      <div>
        <h2 style="margin:0;">🗑️ Recycle Bin - Pasien</h2>
        <div class="muted">Pasien yang telah dihapus</div>
      </div>
      <a class="btn btn-outline" href="?c=patients&a=index">← Kembali ke Daftar</a>
    </div>
  </div>

  <?php if (!empty($_SESSION['flash_message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash_type'] ?? 'info', ENT_QUOTES, 'UTF-8') ?>">
      <?= htmlspecialchars($_SESSION['flash_message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <div class="card">
    <?php if ($total > 0): ?>
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Gender</th>
            <th>Tgl Lahir</th>
            <th>Telepon</th>
            <th>Dihapus Pada</th>
            <th style="width:200px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($patients as $p): ?>
            <tr>
              <td><span class="badge">#<?= (int)$p['id'] ?></span></td>
              <td><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($p['gender'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($p['dob'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($p['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($p['deleted_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
              <td class="actions">
                <button class="btn btn-outline" data-restore data-id="<?= (int)$p['id'] ?>" data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>">♻️ Restore</button>
                <button class="btn btn-danger" data-force-delete data-id="<?= (int)$p['id'] ?>" data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>">❌ Hapus Permanen</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <?php $totalPages = (int)ceil($total / $perPage); if ($totalPages > 1): ?>
        <div class="pagination" style="margin-top:12px;">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === (int)$page): ?>
              <strong class="badge">Hal <?= $i ?></strong>
            <?php else: ?>
              <a class="badge" href="?c=patients&a=recycle&page=<?= $i ?>">Hal <?= $i ?></a>
            <?php endif; ?>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <p class="muted" style="text-align:center; padding: 2rem;">Recycle Bin kosong.</p>
    <?php endif; ?>
  </div>
</div>

<!-- Modal Restore -->
<div id="modal-restore" class="modal-backdrop">
  <div class="modal">
    <div class="modal-header">Konfirmasi Restore</div>
    <div class="modal-body">
      <p>Anda yakin ingin memulihkan pasien <strong id="restore-name"></strong>?</p>
    </div>
    <div class="modal-actions">
      <form id="restore-form" method="post" action="">
        <?= Csrf::field() ?>
        <button type="submit" class="btn">♻️ Ya, Restore</button>
      </form>
      <button type="button" class="btn btn-outline" data-close>Batal</button>
    </div>
  </div>
</div>

<!-- Modal Force Delete -->
<div id="modal-delete" class="modal-backdrop">
  <div class="modal">
    <div class="modal-header">⚠️ Konfirmasi Hapus Permanen</div>
    <div class="modal-body">
      <p>Anda yakin ingin menghapus <strong id="delete-name"></strong> secara permanen?</p>
      <p class="muted" style="font-size: 0.9rem;">Data yang dihapus permanen tidak dapat dipulihkan kembali!</p>
    </div>
    <div class="modal-actions">
      <form id="delete-form" method="post" action="">
        <?= Csrf::field() ?>
        <button type="submit" class="btn btn-danger">❌ Ya, Hapus Permanen</button>
      </form>
      <button type="button" class="btn btn-outline" data-close>Batal</button>
    </div>
  </div>
</div>

<script>
(function() {
  // Restore modal
  const restoreBackdrop = document.getElementById('modal-restore');
  const restoreName = document.getElementById('restore-name');
  const restoreForm = document.getElementById('restore-form');
  
  document.querySelectorAll('[data-restore]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const name = btn.getAttribute('data-name');
      restoreName.textContent = name;
      restoreForm.action = `?c=patients&a=restore&id=${id}`;
      restoreBackdrop.classList.add('show');
    });
  });

  // Force delete modal
  const deleteBackdrop = document.getElementById('modal-delete');
  const deleteName = document.getElementById('delete-name');
  const deleteForm = document.getElementById('delete-form');
  
  document.querySelectorAll('[data-force-delete]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const name = btn.getAttribute('data-name');
      deleteName.textContent = name;
      deleteForm.action = `?c=patients&a=forceDelete&id=${id}`;
      deleteBackdrop.classList.add('show');
    });
  });

  // Close modals
  document.querySelectorAll('[data-close]').forEach(btn => {
    btn.addEventListener('click', () => {
      restoreBackdrop.classList.remove('show');
      deleteBackdrop.classList.remove('show');
    });
  });

  // Close on backdrop click
  [restoreBackdrop, deleteBackdrop].forEach(backdrop => {
    backdrop.addEventListener('click', (e) => {
      if (e.target === backdrop) {
        backdrop.classList.remove('show');
      }
    });
  });
})();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
