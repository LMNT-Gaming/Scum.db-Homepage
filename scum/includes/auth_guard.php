<?php
declare(strict_types=1);

function requireSteamLogin(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!empty($_SESSION['steamid'])) {
        return;
    }

    // Zielseite merken (damit man nach Login zurück kommt)
    $_SESSION['login_next'] = $_SERVER['REQUEST_URI'] ?? '/scum3/index.php?page=home';

    header('Location: /auth/steam_login.php');
    exit;
}
