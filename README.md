# SCUM Homepage

Eine PHP-basierte Weboberfläche für SCUM-Server mit:

* Steam-Login
* Shop-System (Anträge + Gutschein-Käufe)
* Adminbereich
* Vote-/Voucher-System
* Benutzer-Inventar

Die Homepage ist für den Betrieb **im Webroot oder in einem Unterordner** geeignet.
Die `SCUM.db` sollte **niemals direkt vom Dedicated Server** gelesen werden. Immer erst kopieren/synchronisieren (z. B. per Node.js stündlich). Direktes Lesen kann zu Locks oder Fehlern führen.

---

## 📋 Voraussetzungen

* PHP **7.4+**
* MySQL / MariaDB
* Zugriff auf eine **SCUM.db** (SQLite, ReadOnly)
* Steam OpenID Login
* Aktivierte PHP-Erweiterungen:

  * `pdo_mysql`
  * `sqlite3`
  * `curl`
  * `json`

---

## 📁 Verzeichnisstruktur (Auszug)

```
/
├── auth/
│   └── steam_login.php
├── functions/
│   ├── env_function.php
│   ├── db_function.php
│   └── ...
├── includes/
│   ├── adminsteamid.txt
│   └── ...
├── pages/
│   └── shop.php
├── private/
│   ├── .env
│   └── .htaccess
└── index.php
```

---

## 🔐 Secrets & Konfiguration via `.env`

Alle sensiblen Daten (DB-Login, Webhooks, Tokens, Pfade, Base-URL) liegen in:

```
/private/.env
```

Der Ordner **muss** per `.htaccess` gesperrt sein.

**Datei:** `private/.htaccess`

```apache
Require all denied
```

Test im Browser:

```
https://deinedomain.tld/private/.env
```

→ muss **403 Forbidden** liefern.

---

## 🧾 Beispiel: `private/.env`

> Diese Datei **nicht** ins Git committen.

```env
# ===== App =====
APP_ENV=dev
BASE_URL=https://meinehomepage.de
BASE_PATH=/Scum

# ===== Steam Login =====
STEAM_LOGIN_PATH=/auth/steam_login.php

# ===== MySQL =====
MYSQL_HOST=database-xxx.webspace-host.com
MYSQL_DB=dbsxxxxx
MYSQL_USER=dbuxxxxx
MYSQL_PASS=DEIN_PASSWORT
MYSQL_CHARSET=utf8mb4

# ===== SCUM SQLite =====
SCUM_SQLITE_PATH=/mnt/webxxx/htdocs/scum_db/SCUM.db

# ===== Discord Webhooks =====
DISCORD_SHOP_WEBHOOK=https://discord.com/api/webhooks/XXXX/XXXX
DISCORD_NEWS_WEBHOOK=https://discord.com/api/webhooks/XXXX/XXXX

# ===== Top-Games Vote API =====
TOPGAMES_API_BASE=https://api.top-games.net/v1
TOPGAMES_SERVER_TOKEN=DEIN_NEUER_TOKEN
VOTE_VOUCHERS_PER_VOTE=1

# ===== Vehicle Map =====
REQUIRED_ITEM_NAME=Fahrzeugkompass

# ===== Shop Lock (nur anschauen erlaubt) =====
# Leer lassen oder entfernen, um keine Sperre zu aktivieren
SHOP_LOCK_UNTIL=2025-12-24 18:01:01
```

---

## ⚙️ Wichtige Anpassungen

### 1️⃣ Steam Login & Unterordner

Wenn die Homepage in einem Unterordner liegt (z. B. `/Scum/`), wird das über ENV gesteuert:

```env
BASE_PATH=/Scum
STEAM_LOGIN_PATH=/auth/steam_login.php
```

---

### 2️⃣ Admin SteamIDs

**Datei:** `includes/adminsteamid.txt`

Eine SteamID pro Zeile:

```
76561198000000001
76561198000000002
```

---

### 3️⃣ Basis-URL

Die Basis-URL kommt aus `.env`:

```env
BASE_URL=https://meinehomepage.de
```

---

### 4️⃣ Datenbank-Zugang

Die MySQL-Zugangsdaten stehen **nur** in `.env`:

```env
MYSQL_HOST=...
MYSQL_DB=...
MYSQL_USER=...
MYSQL_PASS=...
```

---

## 🧠 SCUM.db (SQLite)

Die SCUM.db wird **read-only** genutzt.

Pfad /scum_db/SCUM.db

---

## 🛒 Shop-System (Kurzfassung)

* Normale Käufe → Antrag (`pending`)
* Voucher-Käufe → sofort genehmigt
* Adminbearbeitung im Adminbereich
* PRG Pattern (kein doppeltes Absenden)

---

## 🔐 Sicherheit

* ❗ Secrets niemals ins Git pushen
* `.env` liegt unter `/private/` und ist gesperrt
* `adminsteamid.txt` schützt den Adminbereich
* Steam Login ist Pflicht

---

## ❓ Fehlersuche

* Leere Seite → `display_errors` aktivieren
* Login-Loop → `BASE_URL`, `BASE_PATH` prüfen
* Kein Shop → MySQL ENV prüfen
* Namen fehlen → `SCUM_SQLITE_PATH` prüfen

---

## 📄 Lizenz

Private Nutzung & Community-Server: ✅
Kommerzielle Nutzung: ❌

---
## SQL Statements:
```
-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Erstellungszeit: 24. Dez 2025 um 22:21
-- Server-Version: 10.11.14-MariaDB-deb11-log
-- PHP-Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `public_stats_consent`
--

CREATE TABLE `public_stats_consent` (
  `steamid` varchar(32) NOT NULL,
  `consent` tinyint(1) NOT NULL DEFAULT 0,
  `show_name` tinyint(1) NOT NULL DEFAULT 1,
  `consented_at` datetime DEFAULT NULL,
  `locked_until` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tabellenstruktur für Tabelle `server_news`
--

CREATE TABLE `server_news` (
  `id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `body` text NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `author_steamid` varchar(32) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `discord_posted_at` datetime DEFAULT NULL,
  `discord_message_id` varchar(64) DEFAULT NULL,
  `discord_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`discord_json`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `shop_categories`
--

CREATE TABLE `shop_categories` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `name` varchar(80) NOT NULL,
  `color` varchar(30) NOT NULL DEFAULT 'rgba(255,255,255,0.35)',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `shop_items`
--

CREATE TABLE `shop_items` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_inventory_item` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `requires_coordinates` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tabellenstruktur für Tabelle `shop_item_prices`
--

CREATE TABLE `shop_item_prices` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `currency` enum('SCUM_DOLLAR','GOLD','VOUCHER') NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Tabellenstruktur für Tabelle `shop_requests`
--

CREATE TABLE `shop_requests` (
  `id` int(11) NOT NULL,
  `steamid` varchar(32) NOT NULL,
  `item_id` int(11) NOT NULL,
  `currency` enum('SCUM_DOLLAR','GOLD','VOUCHER') NOT NULL,
  `price` int(11) NOT NULL,
  `coord_x` int(11) DEFAULT NULL,
  `coord_y` int(11) DEFAULT NULL,
  `status` enum('pending','approved','delivered','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `admin_note` text DEFAULT NULL,
  `handled_by` varchar(32) DEFAULT NULL,
  `handled_at` timestamp NULL DEFAULT NULL,
  `user_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `user_deleted_at` timestamp NULL DEFAULT NULL,
  `voucher_charged` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tabellenstruktur für Tabelle `user_inventory`
--

CREATE TABLE `user_inventory` (
  `id` int(10) UNSIGNED NOT NULL,
  `steamid` varchar(32) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tabellenstruktur für Tabelle `user_vouchers`
--

CREATE TABLE `user_vouchers` (
  `steamid` varchar(32) NOT NULL,
  `vouchers` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Tabellenstruktur für Tabelle `vote_claim_log`
--

CREATE TABLE `vote_claim_log` (
  `id` int(11) NOT NULL,
  `steamid` varchar(32) NOT NULL,
  `playername` varchar(128) NOT NULL DEFAULT '',
  `claimed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `credits_used` int(11) NOT NULL,
  `vouchers_added` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Tabellenstruktur für Tabelle `vote_credits`
--

CREATE TABLE `vote_credits` (
  `steamid` varchar(32) NOT NULL,
  `credits` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vote_players`
--

CREATE TABLE `vote_players` (
  `steamid` varchar(32) NOT NULL,
  `playername` varchar(80) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT 0,
  `usedvotes` int(11) NOT NULL DEFAULT 0,
  `next_claim_after` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indizes für die Tabelle `public_stats_consent`
--
ALTER TABLE `public_stats_consent`
  ADD PRIMARY KEY (`steamid`),
  ADD KEY `idx_consent` (`consent`);

--
-- Indizes für die Tabelle `server_news`
--
ALTER TABLE `server_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_published` (`is_published`),
  ADD KEY `created_at` (`created_at`);

--
-- Indizes für die Tabelle `shop_categories`
--
ALTER TABLE `shop_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indizes für die Tabelle `shop_items`
--
ALTER TABLE `shop_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_shop_items_category` (`category_id`);

--
-- Indizes für die Tabelle `shop_item_prices`
--
ALTER TABLE `shop_item_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_item_currency` (`item_id`,`currency`);

--
-- Indizes für die Tabelle `shop_requests`
--
ALTER TABLE `shop_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_shop_requests_voucher` (`currency`,`voucher_charged`);

--
-- Indizes für die Tabelle `user_inventory`
--
ALTER TABLE `user_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_item` (`steamid`,`item_name`),
  ADD KEY `idx_steamid` (`steamid`);

--
-- Indizes für die Tabelle `user_vouchers`
--
ALTER TABLE `user_vouchers`
  ADD PRIMARY KEY (`steamid`);

--
-- Indizes für die Tabelle `vote_claim_log`
--
ALTER TABLE `vote_claim_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vote_claim_log_steamid` (`steamid`);

--
-- Indizes für die Tabelle `vote_credits`
--
ALTER TABLE `vote_credits`
  ADD PRIMARY KEY (`steamid`);

--
-- Indizes für die Tabelle `vote_players`
--
ALTER TABLE `vote_players`
  ADD PRIMARY KEY (`steamid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `server_news`
--
ALTER TABLE `server_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `shop_categories`
--
ALTER TABLE `shop_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `shop_items`
--
ALTER TABLE `shop_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `shop_item_prices`
--
ALTER TABLE `shop_item_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT für Tabelle `shop_requests`
--
ALTER TABLE `shop_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT für Tabelle `user_inventory`
--
ALTER TABLE `user_inventory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `vote_claim_log`
--
ALTER TABLE `vote_claim_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `shop_items`
--
ALTER TABLE `shop_items`
  ADD CONSTRAINT `fk_shop_items_category` FOREIGN KEY (`category_id`) REFERENCES `shop_categories` (`id`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `shop_item_prices`
--
ALTER TABLE `shop_item_prices`
  ADD CONSTRAINT `fk_prices_item` FOREIGN KEY (`item_id`) REFERENCES `shop_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

```
---
Viel Spaß mit deiner **SCUM Homepage** 🧟‍♂️🔥
