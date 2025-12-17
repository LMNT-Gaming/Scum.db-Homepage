<?php
// Basis-URL deiner Seite (OHNE Slash am Ende)
define('APP_BASE_URL', 'https://deine_URL_zum_verzeichnis.de');

// Wohin nach erfolgreichem Login?
define('APP_LOGIN_REDIRECT', APP_BASE_URL . '/index.php?page=home');

// OpenID Endpoint
define('STEAM_OPENID', 'https://steamcommunity.com/openid/login');
