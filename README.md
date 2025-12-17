# SCUM Homepage

Eine PHP-basierte Weboberfläche für SCUM-Server mit:

* Steam-Login
* Shop-System (Anträge + Gutschein-Käufe)
* Adminbereich
* Vote-/Voucher-System
* Benutzer-Inventar

Die Homepage ist für den Betrieb **im Webroot oder in einem Unterordner** geeignet.
Ihr müsst selber schauen wie Ihr die Scum.db auf euren Webroot bekommt! Ich nutze node.js für den down & upload in den Webspace jede Std einmal.
Das führt zu keinen Crashes! Direktes Lesen empfehle ich unter keinen Umständen von euren Dedicated server etc! Immer zuerst Kopieren!

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
│   ├── db_function.php
│   ├── shop_function.php
│   ├── shop_request_function.php
│   └── ...
├── includes/
│   ├── adminsteamid.txt
│   └── config.php
├── pages/
│   └── shop.php
├── scum_db/SCUM.db
└── index.php
```

---

## ⚙️ Wichtige Anpassungen

### 1️⃣ Steam Login Pfad (auth_guard)

Falls deine Homepage **in einem Unterordner** liegt (z. B. `/Scum/`), muss der Redirect angepasst werden:

```php
header('Location: /auth/steam_login.php');
```

⬇️ ggf. ändern zu:

```php
header('Location: /Scum/auth/steam_login.php');
```

---

### 2️⃣ Admin SteamIDs

**Datei:** `includes/adminsteamid.txt`

Hier müssen **alle SteamIDs der Admins** eingetragen werden, **eine pro Zeile**:

```
76561198000000001
76561198000000002
```

---

### 3️⃣ Basis-URL der Homepage

**Datei:** `includes/config.php`

```php
$config['base_url'] = 'https://meinehomepage.de';
```

> Wichtig für Redirects, Login und Links.

---

### 4️⃣ Datenbank-Zugang (MySQL / MariaDB)

**Datei:** `functions/db_function.php`

```php
$host = 'homepagedatenbankurl';
$db   = 'dbname';
$user = 'user';
$pass = 'pass';
$charset = 'utf8mb4';
```

---

## 🗄️ Datenbank Setup

Für die Homepage wird **eine MySQL / MariaDB** benötigt.

### 📌 Tabellen anlegen

Führe das folgende SQL **komplett** in phpMyAdmin (Tab **SQL**) oder via CLI aus.

<details>
<summary>▶️ SQL Create Statements anzeigen</summary>

```sql
-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Erstellungszeit: 16. Dez 2025 um 12:57
-- Server-Version: 10.11.14-MariaDB-deb11-log
-- PHP-Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `shop_categories` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `name` varchar(80) NOT NULL,
  `color` varchar(30) NOT NULL DEFAULT 'rgba(255,255,255,0.35)',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

INSERT INTO `shop_categories` (`id`, `slug`, `name`, `color`, `sort_order`, `created_at`) VALUES
(1, 'waffen', 'Waffen', '#787878', 0, '2025-12-16 09:02:25'),
(2, 'vehicles', 'Fahrzeuge', '#002afa', 0, '2025-12-16 09:02:46'),
(3, 'basis', 'Basis', '#69e277', 0, '2025-12-16 09:07:27'),
(4, 'homepage', 'Homepage', '#b03030', 0, '2025-12-16 09:07:49'),
(5, 'spezial', 'Spezial', '#9e8900', 0, '2025-12-16 09:13:16');

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

CREATE TABLE `shop_item_prices` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `currency` enum('SCUM_DOLLAR','GOLD','VOUCHER') NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `user_inventory` (
  `id` int(10) UNSIGNED NOT NULL,
  `steamid` varchar(32) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_vouchers` (
  `steamid` varchar(32) NOT NULL,
  `vouchers` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `vote_claim_log` (
  `id` int(11) NOT NULL,
  `steamid` varchar(32) NOT NULL,
  `playername` varchar(128) NOT NULL DEFAULT '',
  `claimed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `credits_used` int(11) NOT NULL,
  `vouchers_added` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `vote_credits` (
  `steamid` varchar(32) NOT NULL,
  `credits` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `vote_players` (
  `steamid` varchar(32) NOT NULL,
  `playername` varchar(80) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT 0,
  `usedvotes` int(11) NOT NULL DEFAULT 0,
  `next_claim_after` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `shop_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

ALTER TABLE `shop_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_shop_items_category` (`category_id`);

ALTER TABLE `shop_item_prices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_item_currency` (`item_id`,`currency`);

ALTER TABLE `shop_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_shop_requests_voucher` (`currency`,`voucher_charged`);

ALTER TABLE `user_inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_item` (`steamid`,`item_name`),
  ADD KEY `idx_steamid` (`steamid`);

ALTER TABLE `user_vouchers`
  ADD PRIMARY KEY (`steamid`);

ALTER TABLE `vote_claim_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vote_claim_log_steamid` (`steamid`);

ALTER TABLE `vote_credits`
  ADD PRIMARY KEY (`steamid`);

ALTER TABLE `vote_players`
  ADD PRIMARY KEY (`steamid`);

ALTER TABLE `shop_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `shop_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `shop_item_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

ALTER TABLE `shop_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

ALTER TABLE `user_inventory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `vote_claim_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `shop_items`
  ADD CONSTRAINT `fk_shop_items_category` FOREIGN KEY (`category_id`) REFERENCES `shop_categories` (`id`) ON UPDATE CASCADE;

ALTER TABLE `shop_item_prices`
  ADD CONSTRAINT `fk_prices_item` FOREIGN KEY (`item_id`) REFERENCES `shop_items` (`id`) ON DELETE CASCADE;

COMMIT;
```

</details>

> ⚠️ Hinweise:
>
> * Engine: **InnoDB**
> * Charset: **utf8mb4** empfohlen
> * Foreign Keys müssen unterstützt werden

---

## 🧠 SCUM.db (SQLite)

Die SCUM.db wird **read-only** genutzt, z. B. für:

* Spielernamen
* SteamID-Zuordnung

Standardpfad:

```php
$path = __DIR__ . '/../scum_db/SCUM.db';
```

Falls deine SCUM.db regelmäßig synchronisiert wird:

* Locks & Copy-Vorgänge werden erkannt
* Die Seite läuft weiter (mit Fallbacks)

---

## 🛒 Shop-System (Kurz erklärt)

* **Normale Käufe** → erzeugen einen Antrag (`pending`)
* **Voucher-Käufe** → werden sofort genehmigt
* Admins bearbeiten Anträge im Adminbereich
* Nutzer können:

  * Anträge abbrechen
  * erledigte Anträge aus ihrer Liste entfernen

Alle Aktionen sind **POST → Redirect → GET** abgesichert (kein doppeltes Absenden).

---

## 🔐 Sicherheit & Hinweise

* ❗ **Webhook-URLs, DB-Zugangsdaten und Secrets niemals ins Git pushen**
* `adminsteamid.txt` schützt den Adminbereich
* Steam Login ist Pflicht – keine Gastzugriffe

---

## ❓ Fehlersuche

* Seite lädt leer → `display_errors` aktivieren
* Login-Loop → Base-URL & Auth-Pfad prüfen
* Kein Shop sichtbar → DB-Verbindung prüfen
* Namen fehlen → SCUM.db Pfad prüfen

---

## 📄 Lizenz / Nutzung

Private Nutzung & Community-Server: ✅
Kommerzielle Nutzung: No!

---

## Vehicle Access Map

Die  Fahrzeugkarte kann aktiviert werden wenn der User ein Shopitem kauft mit dem Namen: "Fahrzeugkompass"
Das kann geändert werden in der map.php:

* const REQUIRED_ITEM_NAME = 'Fahrzeugkompass'; // dein Itemname

---

Viel Spaß mit deiner **SCUM Homepage** 🧟‍♂️🔥
