<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'RS App - Week6', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="/MATERI-ASDOS/Week6/public/assets/styles.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="brand">RS App — Week6</div>
      <?php $current = $_GET['c'] ?? 'patients'; ?>
      <nav class="toolbar">
        <a class="btn <?= ($current==='patients' ? '' : 'btn-outline') ?>" href="/MATERI-ASDOS/Week6/public/?c=patients&a=index">Pasien</a>
        <a class="btn <?= ($current==='appointments' ? '' : 'btn-outline') ?>" href="/MATERI-ASDOS/Week6/public/?c=appointments&a=index">Janji Temu</a>
      </nav>
    </div>
  </header>
  <main class="container" style="padding-top:20px;">
    <?php foreach ((Flash::take() ?? []) as $f): ?>
      <div class="alert <?= $f['type'] === 'success' ? 'alert-success' : 'alert-error' ?>">
        <?= htmlspecialchars($f['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endforeach; ?>
