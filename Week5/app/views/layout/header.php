<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'RS App', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="/MATERI-ASDOS/Week5/public/assets/styles.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="brand">RS App</div>
      <?php $current = $_GET['c'] ?? 'patients'; ?>
      <nav class="toolbar">
        <a class="btn <?= ($current==='patients' ? '' : 'btn-outline') ?>" href="/MATERI-ASDOS/Week5/public/?c=patients&a=index">Pasien</a>
        <!-- Future: Doctors, Appointments -->
      </nav>
    </div>
  </header>
  <main class="container" style="padding-top:20px;">
    <?php if (function_exists('session_status') && session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION['flash'])): ?>
      <?php foreach (($_SESSION['flash'] ?? []) as $f): ?>
        <div class="alert <?= $f['type'] === 'success' ? 'alert-success' : 'alert-error' ?>">
          <?= htmlspecialchars($f['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endforeach; unset($_SESSION['flash']); ?>
    <?php endif; ?>
