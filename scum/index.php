<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
if (!headers_sent()) ob_start();

require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/admin_guard.php';
require_once __DIR__ . '/functions/scum_user_function.php';
// require_once __DIR__ . '/functions/shop_voucher_function.php'; // nur wenn existiert!

requireSteamLogin();   // <<< WICHTIG: zuerst Login erzwingen/Session setzen
initAdminFlag();

$headerStats = [
  'fame' => 0,
  'kuna' => 0,
  'gold' => 0,
  'vouchers' => 0,
];

if (!empty($_SESSION['steamid'])) {
  $steamId = (string)$_SESSION['steamid'];

  $up   = scum_get_user_profile_by_steamid($steamId);
  $upid = (int)($up['id'] ?? 0);

  $headerStats['fame'] = (int)round((float)($up['fame_points'] ?? 0));

  $bal = scum_get_balances_by_user_profile_id($upid);
  $headerStats['kuna'] = (int)($bal['kuna'] ?? 0);
  $headerStats['gold'] = (int)($bal['gold'] ?? 0);

  $headerStats['vouchers'] = function_exists('shop_voucher_count_for_user')
    ? (int)shop_voucher_count_for_user($steamId)
    : 0;
}

$page = $_GET['page'] ?? 'home';
require_once __DIR__ . '/functions/shop_voucher_function.php'; // falls du die hast



if (!empty($_SESSION['steamid'])) {
  $steamId = (string)$_SESSION['steamid'];

  $up = scum_get_user_profile_by_steamid($steamId);
  $upid = (int)($up['id'] ?? 0);

  $headerStats['fame'] = (int)round((float)($up['fame_points'] ?? 0));

  $bal = scum_get_balances_by_user_profile_id($upid);
  $headerStats['kuna'] = (int)($bal['kuna'] ?? 0);
  $headerStats['gold'] = (int)($bal['gold'] ?? 0);

  // Gutscheine kommen NICHT aus scum.db -> das ist deine Shop/MySQL Welt
  $headerStats['vouchers'] = function_exists('shop_voucher_count_for_user')
    ? (int)shop_voucher_count_for_user($steamId)
    : 0;
}

$page = $_GET['page'] ?? 'home';

$routes = [
  'home'        => __DIR__ . '/pages/home.php',
  'shop'        => __DIR__ . '/pages/shop.php',
  'map'         => __DIR__ . '/pages/map.php',
  'squad'       => __DIR__ . '/pages/squad.php',
  'base'       => __DIR__ . '/pages/base_stock.php',
  'vote_rewards' => __DIR__ . '/pages/vote_rewards.php',
  'leaderboard' => __DIR__ . '/pages/leaderboard.php',
  'stats' => __DIR__ . '/pages/stats.php',
  'admin' => __DIR__ . '/pages/admin.php',
];

if (!isset($routes[$page])) {
  http_response_code(404);
  $page = 'home';
}

$currentPage = $page;

include __DIR__ . '/includes/header.php';
include $routes[$page];
include __DIR__ . '/includes/footer.php';
