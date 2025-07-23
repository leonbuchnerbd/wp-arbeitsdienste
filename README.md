# 🎯 Arbeitsdienste Plugin für WordPress

Ein professionelles WordPress-Plugin zur Verwaltung von Arbeitsdiensten für Vereine und Organisationen.

![WordPress](https://img.shields.io/badge/WordPress-5.0+-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg)
![Version](https://img.shields.io/badge/Version-2.1-green.svg)
![License](https://img.shields.io/badge/License-GPL--2.0-blue.svg)

## ✨ Features

### 🏗️ **Arbeitsdienst-Verwaltung**
- ✅ Custom Post Type für Arbeitsdienste
- 🏷️ Manuelle ID-Zuweisung für bessere Organisation
- 📅 Flexible Zeitoptionen (Ganztag, Zeitraum, ab Uhrzeit)
- 👥 Helfer-Management mit maximaler Anzahl
- 📍 Treffpunkt-Angaben

### 📧 **E-Mail-Integration**
- ✉️ Automatische E-Mail-Generierung für Anmeldungen
- 📋 Anpassbare E-Mail-Templates mit Platzhaltern
- 🎯 Individuelle Empfänger pro Arbeitsdienst
- 🔧 Umfassende Einstellungsseite

### 📱 **Responsive Design**
- 📐 Mobile-first Ansatz
- 🎨 Moderne, saubere Kachel-Darstellung
- 🔴 Auffällige Anmelden-Buttons
- 💻 Optimiert für alle Bildschirmgrößen

### 📊 **Export & Verwaltung**
- 📄 CSV-Export aller Arbeitsdienste
- 🔍 Übersichtliche Admin-Oberfläche
- 📈 Sortierung nach Datum
- 🎛️ Umfassende Einstellungsmöglichkeiten

### 🔄 **Auto-Update System**
- 🚀 Automatische Updates über GitHub
- 📢 Update-Benachrichtigungen im WordPress Admin
- 🔒 Sichere Installation über WordPress Update-System
- ⚡ Keine manuellen Downloads erforderlich

## 🚀 Neue Features in v2.0

### ✅ Manuelle ID-Vergabe
- Vergeben Sie eindeutige IDs für jeden Arbeitsdienst (z.B. "AD2025-001")
- Automatische Validierung auf Eindeutigkeit
- ID wird prominent auf den Kacheln angezeigt

### ✅ Flexible Zeitplanung
- **Ganztägig**: Für Arbeitsdienste, die den ganzen Tag dauern
- **Zeitraum**: Mit Start- und Endzeit (z.B. 09:00 - 17:00 Uhr)
- **Ab Uhrzeit**: Für offene Enden (z.B. ab 14:00 Uhr)

### ✅ CSV-Export
- Exportieren Sie alle Arbeitsdienste als CSV-Datei
- Vollständige Daten inklusive aller neuen Felder
- Deutsche Formatierung für Excel-Kompatibilität

### ✅ E-Mail-Integration
- Benutzer können direkt auf Kacheln klicken
- Öffnet vorgefüllte E-Mail an den Verantwortlichen
- Automatisch generierter Anmeldetext mit allen Details

### ✅ Erweiterte Felder
- **Treffpunkt**: Wo sich die Helfer treffen sollen
- **E-Mail des Verantwortlichen**: Für direkte Kontaktaufnahme
- Verbesserte Validierung aller Eingaben

## 📋 Verfügbare Felder

| Feld | Beschreibung | Pflichtfeld |
|------|-------------|-------------|
| Arbeitsdienst-ID | Eindeutige Kennung (z.B. AD2025-001) | Nein |
| Titel | Name des Arbeitsdienstes | Ja |
| Beschreibung | Detaillierte Beschreibung der Tätigkeiten | Nein |
| Datum | Datum des Arbeitsdienstes | Ja |
| Zeittyp | Ganztägig/Zeitraum/Ab Uhrzeit | Ja |
| Startzeit | Beginn (bei Zeitraum/Ab Uhrzeit) | Bedingt |
| Endzeit | Ende (nur bei Zeitraum) | Bedingt |
| Arbeitskreis | Zuständiger Arbeitskreis | Nein |
| Verantwortlicher | Name des Hauptverantwortlichen | Ja |
| E-Mail Verantwortlicher | Kontakt-E-Mail | Empfohlen |
| Benötigte Helfer | Anzahl der benötigten Helfer | Ja |
| Treffpunkt | Wo sich die Helfer treffen | Empfohlen |

## 🎨 Frontend-Darstellung

Die Arbeitsdienste werden als ansprechende Kacheln dargestellt:
- **Grid-Layout**: Responsive Darstellung auf allen Geräten
- **Farbkodierung**: Verschiedene Farben für verschiedene Informationstypen
- **Hover-Effekte**: Interaktive Kacheln mit Animationen
- **Klickbare E-Mail-Links**: Direkter Kontakt zum Verantwortlichen

### Verwendung des Shortcodes

```
[arbeitsdienste]
```

Fügen Sie diesen Shortcode auf jeder Seite oder jedem Beitrag ein, wo die Arbeitsdienste angezeigt werden sollen.

## 🛠 Admin-Bereich

### Arbeitsdienst erstellen/bearbeiten
1. Gehen Sie zu "Arbeitsdienste" → "Neu hinzufügen"
2. Füllen Sie alle relevanten Felder aus
3. Wählen Sie den passenden Zeittyp
4. Speichern Sie den Arbeitsdienst

### CSV-Export
1. Gehen Sie zu "Arbeitsdienste" → "Arbeitsdienst Verwaltung"
2. Klicken Sie auf "Als CSV exportieren"
3. Die Datei wird automatisch heruntergeladen

## 🎯 E-Mail-Integration

Wenn ein Benutzer auf eine Arbeitsdienst-Kachel klickt und eine E-Mail-Adresse hinterlegt ist, öffnet sich automatisch das E-Mail-Programm mit einer vorgefüllten Nachricht:

```
Hallo [Verantwortlicher],

hiermit möchte ich mich für den folgenden Arbeitsdienst anmelden:

Arbeitsdienst: [Titel]
ID: [ID]
Datum: [Datum]
Zeit: [Zeit]
Arbeitskreis: [Arbeitskreis]
Treffpunkt: [Treffpunkt]

Mein Name: [Bitte eintragen]
Meine Telefonnummer: [Bitte eintragen]

Vielen Dank!

Mit freundlichen Grüßen
```

## 📱 Responsive Design & Mobile-Optimierung

Das Plugin ist vollständig responsive und für Mobile-First entwickelt:

### 🎯 Mobile-Optimierungen
- **Touch-optimierte Kacheln**: Größere Touch-Targets (min. 44px)
- **Responsive Grid-Layout**: Automatische Anpassung an Bildschirmgrößen
- **iOS Safari-Optimierung**: Verhindert Zoom beim Fokussieren von Eingabefeldern
- **Android Chrome-Optimierung**: Optimierte Touch-Highlights
- **Orientierungswechsel-Support**: Automatische Layout-Anpassung
- **Viewport-Optimierung**: Korrekte Meta-Tags für mobile Browser

### 📏 Breakpoints
- **Desktop**: > 1200px (Multi-Column Grid)
- **Tablet**: 768px - 1200px (2-Column Grid)
- **Mobile Large**: 414px - 767px (1-Column optimiert)
- **Mobile Standard**: 375px - 413px (1-Column Standard)
- **Mobile Small**: < 375px (1-Column kompakt)

### 🔧 Touch-Optimierungen
- **Haptic Feedback**: Visuelle Rückmeldung bei Touch
- **Swipe-freundlich**: Optimierte Touch-Events
- **Accessibility**: ARIA-Labels und Keyboard-Navigation
- **Performance**: Optimierte Animationen für Touch-Geräte

### 🎨 Admin-Responsive
- **Mobile Admin**: Vollständig responsive Admin-Oberfläche
- **Touch-freundliche Buttons**: Größere Buttons und Eingabefelder
- **Stapelbare Layouts**: Automatische Umordnung auf kleinen Bildschirmen
- **Optimierte Tabellen**: Wichtige Spalten bleiben sichtbar

### 📱 PWA-Ready
- **Install-Prompt**: Unterstützung für "Add to Homescreen"
- **Viewport-Meta**: Korrekte Konfiguration für Web-Apps
- **Theme-Color**: Einheitliche Farben in der Browser-UI

## 🔧 Technische Details

- **WordPress-Version**: 5.0 oder höher
- **PHP-Version**: 7.4 oder höher
- **Custom Post Type**: `arbeitsdienste`
- **CSS-Framework**: Eigenständige, moderne CSS-Grid-Layouts
- **JavaScript**: Vanilla JavaScript für E-Mail-Integration

## 📁 Dateistruktur

```
arbeitsplaene/
├── arbeitsdienste-plugin.php          # Haupt-Plugin-Datei
├── README.md                          # Diese Datei
├── uninstall.php                      # Plugin-Deinstallation
├── admin/
│   ├── arbeitsdienste-admin.php       # Admin-Seiten und CSV-Export
│   └── arbeitsdienste-meta.php        # Meta-Boxen für Custom Fields
├── assets/
│   ├── css/
│   │   ├── admin-style.css            # Admin-Bereich Styling
│   │   ├── public-style.css           # Frontend Styling
│   │   └── mobile-optimizations.css   # Mobile-spezifische Optimierungen
│   └── js/
│       └── mobile-optimizations.js    # Mobile JavaScript-Optimierungen
├── includes/
│   ├── arbeitsdienste-cpt.php         # Custom Post Type Registrierung
│   └── arbeitsdienste-functions.php   # Hilfsfunktionen
└── public/
    └── arbeitsdienste-shortcode.php   # Frontend Shortcode mit Touch-Support
```

## 🚀 Installation

1. Laden Sie den Plugin-Ordner in `/wp-content/plugins/` hoch
2. Aktivieren Sie das Plugin im WordPress Admin-Bereich
3. Erstellen Sie Ihre ersten Arbeitsdienste
4. Fügen Sie den Shortcode `[arbeitsdienste]` auf Ihrer Seite ein

## � Migration von v1.0

Das Plugin ist vollständig rückwärtskompatibel. Bestehende Arbeitsdienste behalten ihre Daten, neue Felder sind optional verfügbar.

## 🎨 Anpassungen

### CSS anpassen
Die Styles können über Ihr Theme angepasst werden. Wichtige CSS-Klassen:
- `.arbeitsdienste-container`: Haupt-Container
- `.arbeitsdienst-kachel`: Einzelne Kachel
- `.arbeitsdienst-anmelden`: Anmelde-Button

### Farben anpassen
Passen Sie die Farbvariablen in `public-style.css` an Ihr Corporate Design an.

## 🐛 Support

Bei Fragen oder Problemen wenden Sie sich an den Plugin-Entwickler.

## 📝 Changelog

### Version 2.0
- ✅ Manuelle ID-Vergabe mit Eindeutigkeitsprüfung
- ✅ Flexible Zeitoptionen (ganztägig/Zeitraum/ab Uhrzeit)
- ✅ CSV-Export-Funktion
- ✅ E-Mail-Integration für direkte Anmeldungen
- ✅ Zusätzliche Felder (Treffpunkt, E-Mail)
- ✅ Verbessertes responsive Design
- ✅ Erweiterte Validierung und Sicherheit

### Version 1.0
- Grundlegende Arbeitsdienst-Verwaltung
- Einfache Kachel-Darstellung
- Basic Custom Post Type

## 🚀 Nutzung

### **1️⃣ Arbeitsdienste im Admin-Bereich verwalten**
- Gehe zu **WordPress-Admin > Arbeitsdienste**  
- Klicke auf **Neuen Arbeitsdienst hinzufügen**  
- Trage die Informationen ein:
  - **Titel:** Name des Arbeitsdienstes  
  - **Arbeitskreis:** Verantwortlicher Bereich  
  - **Datum:** Wann der Arbeitsdienst stattfindet  
  - **Hauptverantwortlicher:** Ansprechpartner  
  - **Benötigte Helfer:** Anzahl der Helfer  
  - **Beschreibung:** Details zum Arbeitsdienst  
- **Speichern** – Der Arbeitsdienst ist jetzt in der Liste! ✅  

---

### **2️⃣ Arbeitsdienste auf der Website anzeigen**
Arbeitsdienste können mit einem **Shortcode** auf einer Seite oder in einem Beitrag angezeigt werden:
[arbeitsdienste]



👉 **Beispiel:**  
1️⃣ Erstelle eine neue **Seite** in WordPress  
2️⃣ Füge den Shortcode `[arbeitsdienste]` ein  
3️⃣ Speichere und veröffentliche die Seite – Fertig! 🎉  

---

### **3️⃣ E-Mail-Anmeldung für Arbeitsdienste**
- Jede Kachel enthält einen **"Anmelden"-Button**  
- Per Klick öffnet sich eine neue E-Mail mit einer **vorgefertigten Nachricht**  
- Der Benutzer kann sich für einen Arbeitsdienst anmelden ✅  

---

## 🗑️ Deinstallation
Falls du das Plugin **dauerhaft entfernen** möchtest:
1️⃣ Gehe zu **Plugins > Installierte Plugins**  
2️⃣ Klicke auf **Deaktivieren**  
3️⃣ Klicke anschließend auf **Löschen**  

💡 **Hinweis:**  
- **Alle gespeicherten Arbeitsdienste & Daten werden entfernt!**  
- Falls du die Daten behalten möchtest, **deaktiviere** das Plugin nur, ohne es zu löschen.

---

## 🔧 Erweiterungsmöglichkeiten
💡 **Das Plugin kann erweitert werden mit:**  
- 🔹 **Filter- und Suchfunktion** für die Arbeitsdienst-Liste  
- 🔹 **Export-Funktion (CSV, Excel)**  
- 🔹 **Status-Verwaltung (Offen, Erledigt, Abgesagt)**  
- 🔹 **REST API-Unterstützung für externe Anwendungen**  

Falls du Ideen hast, melde dich! 🚀😊  

---

## 🛠️ Support & Kontakt
Falls du Fragen oder Probleme hast, kontaktiere mich unter:  
📧 **edv@narrenzunft-badduerrheim.de**  

---

**🚀 Viel Spaß mit dem Arbeitsdienste-Plugin! 🎉**
