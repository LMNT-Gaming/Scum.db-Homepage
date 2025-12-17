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

Viel Spaß mit deiner **SCUM Homepage** 🧟‍♂️🔥
