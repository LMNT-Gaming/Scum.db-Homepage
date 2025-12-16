# SCUM Homepage

Eine PHP-basierte Weboberfläche für SCUM-Server mit:

* Steam-Login
* Shop-System (Anträge + Gutschein-Käufe)
* Adminbereich
* Vote-/Voucher-System
* Benutzer-Inventar

Die Homepage ist für den Betrieb **im Webroot oder in einem Unterordner** geeignet.

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
├── SCUM.db
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

Führe das bereitgestellte SQL-Create-Statement vollständig in phpMyAdmin oder via CLI aus.

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
$path = __DIR__ . '/SCUM.db';
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
Kommerzielle Nutzung: NEIN

---
