<?php $title = 'Pasien - Daftar'; require __DIR__ . '/../layout/header.php'; ?>

  <div class="stack">
    <div class="card">
      <div class="breadcrumb">
        <a href="/MATERI-ASDOS/Week5/public/">Home</a>
        <span class="sep">/</span>
        <span>Pasien</span>
      </div>
      <div style="display:flex; justify-content: space-between; align-items:center; gap:12px;">
        <div>
          <h2 style="margin:0;">Daftar Pasien</h2>
          <div class="muted">Kelola data pasien</div>
        </div>
        <a class="btn" href="?c=patients&a=create">+ Tambah Pasien</a>
      </div>
    </div>

    <div class="card">
      <form class="searchbar" method="get" action="">
        <input type="hidden" name="c" value="patients">
        <input type="hidden" name="a" value="index">
        <input type="text" name="q" placeholder="Cari nama pasien" value="<?= htmlspecialchars((string)($q ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <button class="btn btn-outline" type="submit">Cari</button>
      </form>
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Gender</th>
            <th>Tgl Lahir</th>
            <th>Telepon</th>
            <th style="width:140px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($patients)) : ?>
            <?php foreach ($patients as $p): ?>
              <tr>
                <td><span class="badge">#<?= (int)$p['id'] ?></span></td>
                <td><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($p['gender'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)($p['dob'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)($p['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="actions">
                  <a class="btn btn-outline" href="?c=patients&a=edit&id=<?= (int)$p['id'] ?>">Edit</a>
                  <button class="btn btn-danger" data-open-modal data-id="<?= (int)$p['id'] ?>" data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>">Hapus</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" class="muted" style="text-align:center;">Belum ada data.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <?php
        $totalPages = (int)ceil(($total ?? 0) / ($perPage ?? 10));
        if ($totalPages > 1):
      ?>
        <div class="pagination" style="margin-top:12px;">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === (int)$page): ?>
              <strong class="badge">Hal <?= $i ?></strong>
            <?php else: ?>
              <a class="badge" href="?c=patients&a=index&page=<?= $i ?>&q=<?= urlencode((string)($q ?? '')) ?>">Hal <?= $i ?></a>
            <?php endif; ?>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal Hapus -->
  <div id="modal-backdrop" class="modal-backdrop">
    <div class="modal">
      <div class="modal-header">Konfirmasi Hapus</div>
      <div class="modal-body">
        <p>Anda yakin ingin menghapus pasien <strong id="modal-name"></strong>?</p>
      </div>
      <div class="modal-actions">
        <form id="modal-form" method="post" action="">
          <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </form>
        <button type="button" class="btn btn-outline" id="modal-cancel">Batal</button>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const backdrop = document.getElementById('modal-backdrop');
      const nameEl = document.getElementById('modal-name');
      const form = document.getElementById('modal-form');
      const cancel = document.getElementById('modal-cancel');
      document.querySelectorAll('[data-open-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-id');
          const name = btn.getAttribute('data-name');
          nameEl.textContent = name;
          form.action = `?c=patients&a=delete&id=${id}`;
          backdrop.classList.add('show');
        });
      });
      cancel.addEventListener('click', () => backdrop.classList.remove('show'));
      backdrop.addEventListener('click', (e) => { if (e.target === backdrop) backdrop.classList.remove('show'); });
    })();
  </script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
