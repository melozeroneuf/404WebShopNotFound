# 404WebShopNotFound

## Projektübersicht

Dieses Repository dient als Grundlage für ein Webshop-Projekt im Rahmen der Lehrveranstaltungen **Webtechnologien** und **Web Scripting**.

Ziel ist die Entwicklung eines vollständigen Webshops mit klarer Trennung zwischen **Frontend** und **Backend**, inklusive Benutzerverwaltung, Produktverwaltung, Warenkorb, Bestellungen, Rechnungen, Gutscheinen und Administrationsbereich.

Die Umsetzung orientiert sich an der vorgegebenen Projektspezifikation.

---

## Ziel des Projekts

Der Webshop soll einen vollständigen und nachvollziehbaren Kaufprozess abbilden. Dabei müssen sowohl Gäste als auch registrierte Benutzer und Administratoren passende Funktionen zur Verfügung haben.

Wichtige technische Anforderungen:

* klare Trennung von Frontend und Backend
* Datenaustausch zwischen Frontend und Backend über JSON
* objektorientierter Ansatz in PHP
* Nutzung von HTML, CSS, Bootstrap
* Einsatz von JavaScript, jQuery und AJAX
* Verwendung von Sessions und Cookies
* Datenhaltung in einer MySQL-Datenbank
* Unterstützung von Fileuploads

---

## Basisfunktionen laut Aufgabenstellung

Der Webshop soll mindestens folgende Bereiche enthalten:

* Präsentation und Verwaltung von Produkten
* Warenkorbfunktionalität
* Kundenverwaltung
* Bestellungen und Rechnungen
* Gutscheine verwalten und einlösen
* Administrationsbereich

---

## Benutzerrollen

### Gast

Nicht registrierte Benutzer können:

* Produkte ansehen
* Produkte suchen
* Produkte in den Warenkorb legen
* Warenkorb bearbeiten
* sich registrieren
* sich einloggen

### Registrierter Benutzer

Zusätzlich zu den Gast-Funktionen können registrierte Benutzer:

* sich ausloggen
* Produkte bestellen
* Bestellungen und Rechnungen einsehen
* eigene Daten bearbeiten
* Zahlungsmöglichkeiten verwalten

### Administrator

Administratoren können:

* Produkte anlegen, bearbeiten und löschen
* Kunden verwalten
* Gutscheine erstellen und verwalten
* Bestellungen einsehen
* Kunden deaktivieren

---

## Empfohlene Projektstruktur

```text
/frontend
  index.html
  /res
    /css
      style.css
    /js
      script.js
    /img
  /sites
    cart.html
    login.html
    register.html
    account.html
    admin-products.html
    admin-users.html
    admin-coupons.html
    terms.html
    imprint.html

/backend
  /config
    dbaccess.php
    dataHandler.php
  /models
    user.class.php
    product.class.php
    order.class.php
    coupon.class.php
  /logic
    requestHandler.php
    authHandler.php
    cartHandler.php
    orderHandler.php
    adminHandler.php
  /productpictures

/database
  webshop.sql

/readme.txt
```

---

## Architektur

### Frontend

Das Frontend ist zuständig für:

* Darstellung der Seiten
* Benutzerinteraktion
* Formulare
* AJAX-Requests an das Backend
* Anzeigen von Produktdaten, Warenkorb, Konto und Admin-Oberflächen

### Backend

Das Backend ist zuständig für:

* Verarbeitung von Requests
* Validierung von Daten
* Kommunikation mit der Datenbank
* Login-Logik
* Session- und Cookie-Verwaltung
* Geschäftslogik für Bestellungen, Gutscheine und Administration

### Kommunikation

Die Kommunikation zwischen Frontend und Backend erfolgt über **JSON**.

Beispiel:

* Frontend sendet Produkt-ID an Backend
* Backend verarbeitet die Anfrage
* Backend liefert JSON-Antwort zurück
* Frontend aktualisiert die Oberfläche ohne Seiten-Reload

---

## Empfohlene Datenbanktabellen

### users

* id
* salutation
* firstname
* lastname
* address
* zip
* city
* email
* username
* password_hash
* payment_info
* is_admin
* is_active
* created_at

### products

* id
* name
* description
* rating
* price
* image_path
* category_id
* active
* created_at

### categories

* id
* name

### carts

* id
* user_id nullable
* session_id
* created_at

### cart_items

* id
* cart_id
* product_id
* quantity

### orders

* id
* user_id
* total_amount
* payment_method
* coupon_id nullable
* invoice_number nullable
* created_at

### order_items

* id
* order_id
* product_id
* product_name
* product_price
* quantity

### coupons

* id
* code
* value
* valid_until
* remaining_value
* is_used
* created_at

### invoices

* id
* order_id
* invoice_number
* created_at

---

## Entwicklungsplan

Damit das Projekt übersichtlich bleibt, sollte die Umsetzung in Etappen erfolgen.

### Phase 1: Grundstruktur

* Ordnerstruktur erstellen
* Datenbank anlegen
* zentrale DB-Service-Klasse erstellen
* Basislayout mit Navigation vorbereiten

### Phase 2: Registrierung und Login

* Registrierungsformular erstellen
* HTML5- und JavaScript-Validierung ergänzen
* serverseitige Validierung umsetzen
* Passwort doppelt abfragen und vergleichen
* Passwort gehasht speichern
* Login mit Benutzername oder E-Mail ermöglichen
* Session setzen
* Option „Login merken“ über Cookie umsetzen
* Logout implementieren

### Phase 3: Produktübersicht

* Kategorien anzeigen
* Produkte aus Datenbank laden
* Standardkategorie vorauswählen
* Anzeige von Name, Bild, Preis und Bewertung
* Produktdaten per JSON laden

### Phase 4: Warenkorb

* Produkt per Button in den Warenkorb legen
* AJAX ohne Reload verwenden
* Warenkorb-Zähler live aktualisieren
* Produkte im Warenkorb auflisten
* Mengen erhöhen und Produkte entfernen
* optional Drag-and-Drop auf Warenkorb-Symbol

### Phase 5: Produktsuche

* Suchfeld in Produktübersicht integrieren
* Suche bei jeder Eingabe starten
* Ergebnisse live per AJAX laden

### Phase 6: Bestellung

* Bestellung nur für eingeloggte Benutzer
* Warenkorb nach Login erhalten
* Zahlungsmethode auswählen
* Gutschein einlösen
* Bestellung in Datenbank speichern
* order und order_items anlegen

### Phase 7: Mein Konto

* Benutzerdaten anzeigen
* Benutzerdaten bearbeiten
* Passwort zur Bestätigung verlangen
* Zahlungsmöglichkeiten ergänzen
* Bestellungen nach Datum sortiert anzeigen
* Rechnungsansicht in neuem Fenster erzeugen
* Rechnungsnummer generieren

### Phase 8: Administratorbereich

* Admin-Login nutzen
* Produkte anlegen, bearbeiten, löschen
* Produktbilder hochladen
* Kundenliste anzeigen
* Kunden deaktivieren
* Bestelldetails einsehen
* einzelne Produkte aus Bestellungen entfernen
* Gutscheine erzeugen und verwalten

---

## Detaillierte Anforderungen nach Bereich

### 1. Registrierung

Bei der Registrierung müssen folgende Daten erfasst werden:

* Anrede
* Vorname
* Nachname
* Adresse
* PLZ
* Ort
* E-Mail-Adresse
* Benutzername
* Passwort
* Passwort-Wiederholung
* Zahlungsinformationen

Anforderungen:

* Validierung im Frontend und Backend
* alle Felder auf Vollständigkeit prüfen
* Passwort zweimal eingeben
* Passwörter auf Übereinstimmung prüfen
* Passwort verschlüsselt speichern
* Administrator manuell in der DB anlegen

### 2. Login

Anforderungen:

* Login per Benutzername oder E-Mail-Adresse
* Passwortprüfung gegen Datenbank
* Login-Status sichtbar darstellen
* Logout-Button für eingeloggte Benutzer
* unterschiedliche Menüs je nach Rolle
* Cookie für „Login merken“
* Session beim Schließen des Browsers beenden, außer Cookie ist gesetzt

### 3. Produkte

Anforderungen:

* Produkte nach Kategorien anzeigen
* Standardkategorie vorauswählen
* Name, Bild, Preis und Bewertung anzeigen
* Produkt per Link in Warenkorb legen
* AJAX ohne Seitenreload
* Warenkorbzähler dynamisch aktualisieren
* optional Drag-and-Drop

### 4. Produktsuche

Anforderungen:

* Suchfeld mit Continuous Search Filter
* Suche startet bereits bei Eingabe eines Buchstabens
* Ergebnisse ohne Reload aktualisieren

### 5. Bestellung

Anforderungen:

* Bestellung nur für eingeloggte Benutzer
* Warenkorb nach Login erhalten
* Zahlungsart oder Gutschein auswählen
* Gutscheinwert korrekt abziehen
* Restwert des Gutscheins speichern
* unvollständige Daten verhindern Bestellabschluss
* Bestellung in DB speichern
* Bestellung später nachvollziehbar anzeigen

### 6. Konto

Anforderungen:

* Stammdaten anzeigen und bearbeiten
* sensible Informationen nicht vollständig anzeigen
* Passwort bei Änderungen verlangen
* neue Zahlungsmöglichkeiten hinzufügen
* Bestellungen nach Da
