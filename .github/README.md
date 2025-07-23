# 🚀 GitHub Actions Workflow für automatische Releases

## Was passiert automatisch:

### **🎯 Trigger:**
- Wird ausgelöst bei jedem Git Tag Push
- Beispiel: `git tag 2.3 && git push origin 2.3`

### **📦 Automatische Schritte:**

1. **📋 Version extrahieren** aus Git Tag
2. **📄 Release Notes generieren** mit Standard-Template
3. **🗜️ ZIP-Datei erstellen** mit sauberen Plugin-Dateien (ohne Dev-Files)
4. **🚀 GitHub Release erstellen** mit:
   - Titel: "Version X.X"
   - Detaillierte Release Notes
   - ZIP-Download für manuelle Installation
5. **✅ Auto-Update triggern** für alle WordPress-Installationen

## 📋 Workflow Verwendung:

### **Einfacher Release:**
```bash
# 1. Version setzen
php update-version.php 2.3

# 2. Git-Befehle ausführen (vom Script angezeigt)
git add .
git commit -m "🚀 Version 2.3"
git tag 2.3
git push origin main --tags
```

### **Was dann automatisch passiert:**
1. ⚡ GitHub Actions startet automatisch
2. 📦 ZIP-Datei wird erstellt: `arbeitsdienste-plugin-v2.3.zip`
3. 🌐 Release wird veröffentlicht
4. 📢 Auto-Update-System benachrichtigt alle WordPress-Installationen

## 🎯 Vorteile:

- ✅ **Konsistent:** Immer gleiche Release-Struktur
- ⚡ **Schnell:** 2-3 Minuten vom Tag bis zum Release
- 🔒 **Sicher:** Nur saubere Plugin-Dateien im ZIP
- 📱 **Professionell:** Automatische Release Notes
- 🚀 **Benutzerfreundlich:** Direkte Update-Benachrichtigungen

## 📁 Was in der ZIP-Datei enthalten ist:

```
arbeitsdienste-plugin-v2.3.zip
└── arbeitsdienste-plugin/
    ├── arbeitsdienste-plugin.php    # Haupt-Plugin
    ├── uninstall.php               # Deinstallations-Hook
    ├── admin/                      # Admin-Interface
    ├── assets/                     # CSS/JS/Bilder
    ├── includes/                   # PHP-Klassen
    └── public/                     # Frontend-Code
```

## 🚫 Was NICHT enthalten ist:

- ❌ Development-Scripts (`update-version.php`, `dev.bat`)
- ❌ GitHub-Konfiguration (`.github/`)
- ❌ Dokumentation (`*.md` Dateien)
- ❌ Git-History

## 🔧 Workflow anpassen:

Die Datei `.github/workflows/release.yml` kann angepasst werden für:
- 📝 Andere Release Notes
- 📦 Zusätzliche Dateien im ZIP
- 🎯 Andere Trigger-Bedingungen
- 📊 Zusätzliche Validierungen

---

**💡 Nach jedem Release werden alle WordPress-Installationen automatisch benachrichtigt!**
