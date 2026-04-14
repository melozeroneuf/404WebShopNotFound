# 404WebShopNotFound

Ein Webshop-Projekt im Rahmen der Lehrveranstaltungen Webtechnologien und Web Scripting.
Der Shop ermöglicht die Verwaltung von Produkten, Kunden und Bestellungen sowie einen vollständigen Kaufprozess inklusive Warenkorb.

## Funktionen

* Produktverwaltung (anzeigen, hinzufügen, bearbeiten)
* Warenkorb-System
* Benutzerregistrierung und Login
* Bestellungen und Rechnungen
* Gutscheinsystem
* Administrationsbereich

## Technologien

Frontend:

* HTML, CSS, Bootstrap
* JavaScript, jQuery, AJAX

Backend:

* PHP (objektorientiert)
* Sessions & Cookies
* MySQL-Datenbank

## Projektstruktur

Das Projekt ist in Frontend und Backend getrennt aufgebaut.

Frontend:

* HTML-Seiten (z. B. index.html)
* CSS-Dateien für Styling
* JavaScript für Interaktionen und API-Calls

Backend:

* PHP-Klassen für:

  * Benutzer (User)
  * Produkte (Product)
* Datenbankzugriff über zentrale Serviceklasse
* Request-Handling über API (JSON)

## Installation & Start

1. Repository klonen:

```bash
git clone https://github.com/melozeroneuf/404WebShopNotFound.git
```

2. Projekt in Webserver kopieren (z. B. XAMPP htdocs)

3. Datenbank importieren:

* MySQL starten
* mitgelieferte .sql Datei importieren

4. Konfiguration anpassen:

* DB-Zugangsdaten in config-Datei setzen

5. Projekt starten:

* Browser öffnen: http://localhost/404WebShopNotFound

## API Kommunikation

Das Frontend kommuniziert mit dem Backend über JSON.
Dadurch ist es möglich, alternative Frontends (z. B. Mobile Apps) anzubinden.

## Hinweise

* Der Webshop folgt einem klaren Kaufprozess ohne Dead-Ends
* Code ist kommentiert für bessere Nachvollziehbarkeit
* Objektorientierter Ansatz wurde verwendet

## Autor

Alcikaya Melih

