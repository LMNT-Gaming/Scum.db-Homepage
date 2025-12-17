<?php ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL); 

require_once __DIR__ . '/../functions/scum_db.php';

$scumDbState = scum_db_status(); // ok/reason

?>
<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SCUM Dashboard</title>

    <link rel="stylesheet" href="assets/theme.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<style>
    


</style>

<body>
    <body>
<?php
require_once __DIR__ . '/../functions/scum_db.php';
$scum = scum_db_status();
?>
<div id="scumSyncModal" class="scum-sync-modal <?= !$scum['ok'] ? 'open' : '' ?>">
  <div class="scum-sync-card warning">
    <div class="scum-sync-header">
      <div class="scum-sync-icon">!</div>
      <div class="scum-sync-title">SCUM.db wird aktualisiert</div>
    </div>

    <div class="scum-sync-text">
      Die Spieldaten werden gerade übertragen.<br>
      Einige Bereiche sind kurzzeitig nicht verfügbar.
    </div>

    <div class="scum-sync-actions">
      <button class="btn btn-warning" onclick="location.reload()">Neu laden</button>
      <button class="btn btn-ghost" onclick="document.getElementById('scumSyncModal').classList.remove('open')">Schließen</button>
    </div>

    <div class="scum-sync-sub muted">
      Grund: <?= htmlspecialchars($scum['reason']) ?>
    </div>
  </div>
</div>


  <header class="topbar-wrap">
    <nav class="topbar" aria-label="Hauptnavigation">
      <!-- Mobile: Burger -->
      <button class="nav-burger" id="navBurger" type="button" aria-label="Menü öffnen" aria-expanded="false">☰</button>
      <div class="nav-brand">SCUM</div>

      <!-- CENTER: Desktop Nav -->
      <div class="nav-links" id="navLinks">
        <a class="nav-btn <?= $currentPage === 'home' ? 'active' : '' ?>" href="index.php?page=home">Home</a>
        <a class="nav-btn <?= $currentPage === 'shop' ? 'active' : '' ?>" href="index.php?page=shop">Shop</a>
        <a class="nav-btn <?= $currentPage === 'map' ? 'active' : '' ?>" href="index.php?page=map">Serverkarte</a>
        <a class="nav-btn <?= $currentPage === 'squad' ? 'active' : '' ?>" href="index.php?page=squad">Squad</a>
        <a class="nav-btn <?= $currentPage === 'vote_rewards' ? 'active' : '' ?>" href="index.php?page=vote_rewards">Servervotes</a>
        <a class="nav-btn <?= $currentPage === 'stats' ? 'active' : '' ?>" href="index.php?page=stats">Stats</a>
        <?php if (!empty($_SESSION['isAdmin'])): ?>
          <a class="nav-btn <?= $currentPage === 'admin' ? 'active' : '' ?>" href="index.php?page=admin">Admincenter</a>
        <?php endif; ?>

        <?php if (!empty($_SESSION['steamid'])): ?>
          <a class="nav-btn" href="auth/logout.php">Logout</a>
        <?php endif; ?>
      </div>

      <!-- RIGHT: Stats -->
      <div class="header-stats">
        <div class="stat stat-fame">
          <img src="assets/img/icons/fame.png" alt="">
          <span><?= number_format($headerStats['fame'], 0, ',', '.') ?></span>
        </div>
        <div class="stat stat-gold">
          <img src="assets/img/icons/gold.png" alt="">
          <span><?= number_format($headerStats['gold'], 0, ',', '.') ?></span>
        </div>
        <div class="stat stat-kuna">
          <img src="assets/img/icons/kuna.png" alt="">
          <span><?= number_format($headerStats['kuna'], 0, ',', '.') ?></span>
        </div>
        <div class="stat stat-voucher">
          <img src="assets/img/icons/voucher.png" alt="">
          <span><?= number_format($headerStats['vouchers'], 0, ',', '.') ?></span>
        </div>
      </div>
    </nav>

    <!-- Mobile Drawer (NUR EINMAL) -->
    <div class="nav-drawer" id="navDrawer" aria-hidden="true">
      <div class="nav-drawer-inner">
        <div class="nav-drawer-head">
          <div class="nav-drawer-title">Menü</div>
          <button class="nav-close" id="navClose" type="button" aria-label="Menü schließen">✕</button>
        </div>

        <div class="nav-drawer-links">
          <a class="nav-btn <?= $currentPage === 'home' ? 'active' : '' ?>" href="index.php?page=home">Home</a>
          <a class="nav-btn <?= $currentPage === 'shop' ? 'active' : '' ?>" href="index.php?page=shop">Shop</a>
          <a class="nav-btn <?= $currentPage === 'map' ? 'active' : '' ?>" href="index.php?page=map">Serverkarte</a>
          <a class="nav-btn <?= $currentPage === 'squad' ? 'active' : '' ?>" href="index.php?page=squad">Squad</a>
          <a class="nav-btn <?= $currentPage === 'vote_rewards' ? 'active' : '' ?>" href="index.php?page=vote_rewards">Servervotes</a>

          <?php if (!empty($_SESSION['isAdmin'])): ?>
            <a class="nav-btn <?= $currentPage === 'admin' ? 'active' : '' ?>" href="index.php?page=admin">Admincenter</a>
          <?php endif; ?>

          <?php if (!empty($_SESSION['steamid'])): ?>
            <a class="nav-btn" href="auth/logout.php">Logout</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

    <div class="nav-drawer" id="navDrawer" aria-hidden="true">
        <div class="nav-drawer-inner">
            <div class="nav-drawer-head">
                <div class="nav-drawer-title">Menü</div>
                <button class="nav-close" id="navClose" type="button" aria-label="Menü schließen">✕</button>
            </div>

            <div class="nav-drawer-links">
                <!-- deine Links -->
                <a class="nav-btn <?= $currentPage === 'home' ? 'active' : '' ?>" href="index.php?page=home">Home</a>
                <a class="nav-btn <?= $currentPage === 'shop' ? 'active' : '' ?>" href="index.php?page=shop">Shop</a>
                <a class="nav-btn <?= $currentPage === 'map' ? 'active' : '' ?>" href="index.php?page=map">Karte & Quests</a>
                <a class="nav-btn <?= $currentPage === 'squad' ? 'active' : '' ?>" href="index.php?page=squad">Squad</a>
                <a class="nav-btn <?= $currentPage === 'vote_rewards' ? 'active' : '' ?>" href="index.php?page=vote_rewards">Servervotes</a>
                <!--<a class="nav-btn <?= $currentPage === 'leaderboard' ? 'active' : '' ?>" href="index.php?page=leaderboard">Leaderboard</a>-->
                <?php if (!empty($_SESSION['isAdmin'])): ?>
                    <a class="nav-btn <?= $currentPage === 'admin' ? 'active' : '' ?>" href="index.php?page=admin">Admincenter</a>
                <?php endif; ?>
                <?php if (!empty($_SESSION['steamid'])): ?>
                    <a class="nav-btn" href="auth/logout.php">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- <div style="font-size:12px;opacity:.75;padding:6px 12px;">
        steamid: <?= htmlspecialchars($_SESSION['steamid'] ?? 'NONE') ?> |
        isAdmin: <?= isset($_SESSION['isAdmin']) ? ($_SESSION['isAdmin'] ? 'true' : 'false') : 'UNSET' ?> |
        checked: <?= isset($_SESSION['isAdminChecked']) ? 'yes' : 'no' ?>
    </div>-->