<?php $title = 'Pasien - Week6'; require __DIR__ . '/../layout/header.php'; ?>
<div class="stack">
  <div class="card">
  <div class="breadcrumb"><a href="/MATERI-ASDOS/Week6/public/">Beranda</a><span class="sep">/</span><a href="?c=patients&a=index">Pasien</a></div>
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
          <th><a href="?c=patients&a=index&sort=id&dir=<?= ($sort==='id' && $dir==='ASC')?'DESC':'ASC' ?>&q=<?= urlencode((string)($q ?? '')) ?>">ID</a></th>
          <th><a href="?c=patients&a=index&sort=name&dir=<?= ($sort==='name' && $dir==='ASC')?'DESC':'ASC' ?>&q=<?= urlencode((string)($q ?? '')) ?>">Nama</a></th>
          <th>Gender</th>
          <th><a href="?c=patients&a=index&sort=dob&dir=<?= ($sort==='dob' && $dir==='ASC')?'DESC':'ASC' ?>&q=<?= urlencode((string)($q ?? '')) ?>">Tgl Lahir</a></th>
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
    <?php $totalPages = (int)ceil(($total ?? 0) / ($perPage ?? 10)); if ($totalPages > 1): ?>
      <div class="pagination" style="margin-top:12px;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <?php if ($i === (int)$page): ?>
            <strong class="badge">Hal <?= $i ?></strong>
          <?php else: ?>
            <a class="badge" href="?c=patients&a=index&page=<?= $i ?>&q=<?= urlencode((string)($q ?? '')) ?>&sort=<?= urlencode($sort) ?>&dir=<?= urlencode($dir) ?>">Hal <?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<div id="modal-backdrop" class="modal-backdrop"><div class="modal"><div class="modal-header">Konfirmasi Hapus</div><div class="modal-body"><p>Anda yakin ingin menghapus pasien <strong id="modal-name"></strong>?</p></div><div class="modal-actions"><form id="modal-form" method="post" action=""><button type="submit" class="btn btn-danger">Ya, Hapus</button></form><button type="button" class="btn btn-outline" id="modal-cancel">Batal</button></div></div></div>
<script>(function(){const b=document.getElementById('modal-backdrop');const n=document.getElementById('modal-name');const f=document.getElementById('modal-form');const c=document.getElementById('modal-cancel');document.querySelectorAll('[data-open-modal]').forEach(btn=>{btn.addEventListener('click',()=>{const id=btn.getAttribute('data-id');const name=btn.getAttribute('data-name');n.textContent=name;f.action=`?c=patients&a=delete&id=${id}`;b.classList.add('show');});});c.addEventListener('click',()=>b.classList.remove('show'));b.addEventListener('click',(e)=>{if(e.target===b)b.classList.remove('show');});})();</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>