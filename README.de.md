# Jardis Logger
![Build Status](https://github.com/lane4hub/logger/actions/workflows/ci.yml/badge.svg)

Jardis Logger ist ein PSR-3 erweitertes **flexibles und anpassbares Logging-System** für PHP, das sich ideal für kontextbasiertes Logging – wie z. B. im **Domain-Driven Design (DDD)** – und die nahtlose Integration in Ihre Projekte eignet.

---

**Logger** ist eine sehr leistungsfähige und flexible PHP-Bibliothek, welche die Protokollierung (Logging) von Nachrichten in einem kontextbasierten System unterstützt.

Es können Strukturen von Datalogs beliebig angepasst werden, Protokollierungs-Handlern und spezifische Formatter hinzugefügt werden, um die Log-Meldungen individuell zu gestalten und formatieren.

## Funktionen

- **Protokollstufen:** Unterstützt alle gängigen Stufen (Debug, Info, Notice, Warning, Error, Critical, Alert, Emergency).
- **Kontextbasiertes Logging:** Jede Logger-Instanz kann mit einem spezifischen Kontext erstellt werden, was eine klar strukturierte Log-Ausgabe ermöglicht – ideal für die Modellierung in Systemen nach **Domain-Driven Design**.
- **Anpassbare Protokollierungs-Handler:** Nutzen Sie beliebige Implementierungen von `LogCommandInterface`, um die Protokollierungslogik an Ihre Bedürfnisse anzupassen.
- **Anpassbare Log Datal:** Erweitere nach Deine Wünschen die Daten für die Ausgabe in die Logs je LogLevel.
- **Formatter für die Ausgabe:** Stelle sicher, dass Protokolle vor der Ausgabe individuell formatiert werden können
- **Log History:** Lass dir auf Wunsch eine begrenzbare Log History je Log-Level zurückliefern.

## Vorhandene Logger Commands
- LogConsole
- LogFile
- LogDatabase
- LogRedis
- LogErrorLog
- LogSysLog
- LogSlack
- LogStash
- LogNull
- *... erweiterbar nach deinen Wünschen!*

## Vorhandene Log Data Formate
- LogData (Standard mit Context, Loglevel und Message)
  - LogDateTime
  - LogClientIp
  - LogMemoryUsage
  - LogMemoryPeak
  - LogUuid
  - LogWebRequest
  - *... erweiterbar nach deinen Wünschen!*

## Vorhandene Logger Output Formate
- LogLineFormat
- LogJsonFormat
- LogHumanFormat
- *... erweiterbar nach deinen Wünschen!*
---

## Verwendung der Jardis Logger

### Beispielcode zum erstellen eines Loggers mit 2 LogCommands

Ein Logger benötigt bei seiner Erstellung einen expliziten Kontext (z. B. den Namen einer spezifischen Domäne, Komponente oder Anwendung) und mindestens einen Handler, der die Log-Nachrichten verarbeitet.

```php
use Jardis\Logger\Logger;
use Jardis\Logger\command\LogFile;
use Jardis\Logger\command\LogConsole;
use Jardis\Logger\command\LoggerInterface;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');
$logger->addHandler(new LogConsole(LogLevel::LOG_INFO))
$logger->addHandler(new LogFile(LogLevel::LOG_DEBUG, 'pathToLogFile'))
```

### Beispielcode  zur Protokollierung

```php
$logger->debug('Dies ist eine Debug-Nachricht', ['extra' => 'Debug-Daten']);
$logger->info('Dies ist eine Info-Nachricht');
$logger->error('Ein Fehler ist aufgetreten!', ['details' => 'Fehlerdetails']);
```

### Beispielcode zur Erweiterung der Datenausgabe

Formatter dienen dazu, die Log-Nachrichten vor der Ausgabe unterschiedlich zu formatieren. Beispielsweise können Logs im JSON-Format ausgegeben werden.

Per Default wir ein TextLogger verwendet.

```php
use Jardis\Logger\command\LogConsole;
use Jardis\Logger\command\LoggerInterface;
use Jardis\Logger\Logger;
use Jardis\Logger\service\format\LogJsonFormat;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');

$logConsole = (new LogConsole(LogLevel::LOG_INFO, 'pathToFile'))->setFormat(new LogJsonFormat());
$logger->addHandler($logConsole)

$logger->info('Das wird nun im JsonFormat ausgegeben!', ['details' => 'infos']);
```

### Beispielcode zum Austausch eines Output-Formatters je LogCommand und ERweiterung der Datenausgabe (Optional)

Formatter dienen dazu, die Log-Nachrichten vor der Ausgabe unterschiedlich zu formatieren. Beispielsweise können Logs im JSON-Format ausgegeben werden.

Per Default wir ein TextLogger verwendet.

```php
use Jardis\Logger\command\LogFile;
use Jardis\Logger\command\LogSlack;
use Jardis\Logger\command\LoggerInterface;
use Jardis\Logger\service\format\LogJsonFormat;
use Jardis\Logger\service\format\LogHumanFormat;
use \Jardis\Logger\service\logData\LogWebRequestData;
use Jardis\Logger\Logger;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');

$logFile = (new LogFile(LogLevel::LOG_INFO, 'pathToFile'))->setFormat(new LogJsonFormat());
$logSlack = (new LogSlack(LogLevel::LOG_ERROR,'webHookUrl'))->setFormat(new LogHumanFormat());
$logger->addHandler($logFile);
$logger->addHandler($logSlack);
$logger->addLogData(LogLevel::LOG_ERROR, new LogWebRequestData());
```

---

## Quickstart mit Composer

Installieren Sie das Paket über Composer:

```sh
composer require lane4hub/logger
```

## Quickstart github

```sh
git clone https://github.com/lane4hub/logger.git
cd logger
```

**Wenn du LogDatabase benutzen willst, dann musst du vorher eine entsprechende Tabelle in deiner Detenbank erzeugen.**

```sql
"CREATE TABLE logContextData (
    id INT AUTO_INCREMENT PRIMARY KEY,
    context TEXT NOT NULL,
    level VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
);"
```
**Du kannst optional auch den vorgegebenen TabellenName ändern und/oder weitere Felder hinzufügen.**

### Beispielcode zur Verwendung LogDatabase mit Default Struktur

```php
use Jardis\Logger\Logger;
use Jardis\Logger\command\LogDatabase;
use Jardis\Logger\command\LoggerInterface;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');

$logDatabase = (new LogDatabase(LogLevel::LOG_INFO, $yourPDO));
$logger->addHandler($logDatabase)

$logger->info('Log into database', ['details' => 'data']);
```

### Beispielcode zur Verwendung LogDatabase mit eigenem Tabellen Namen und erweiterte Felder

```php
use Jardis\Logger\Logger;
use Jardis\Logger\command\LogDatabase;
use Jardis\Logger\command\LoggerInterface;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');

$logDatabase = (new LogDatabase(LogLevel::LOG_INFO, $yourPDO));
//new column in logData
$logDatabase->logData()->addLogData('myOwnContent', fn() => 'that is myOwnContent');
//new column in user context data
$logDatabase->logData()->addUserLogData('addUserField', fn() => 'userContent');
$logger->addHandler($logDatabase)
```


---

## Architektur

### Kernklassen

- **Logger:** Zentraler Einstiegspunkt, der die Methoden für das Logging verschiedener Stufen sowie die Verwaltung von Handlers und Formatters bereitstellt.
- **LogCommandInterface:** Definiert die Schnittstelle für Protokollierungs-Handler. Jeder Handler verarbeitet die Logik individuell.
- **LogFormatInterface:** Definiert die Schnittstelle für benutzerdefinierte Formatter. Formatter formatieren die Log-Ausgaben auf eine spezifische Weise.

### Unterstützte Protokollstufen

| Stufe       | Beschreibung                            |
|-------------|----------------------------------------|
| DEBUG       | Für detaillierte Fehler- und Systemmeldungen |
| INFO        | Für allgemeine Informationen           |
| NOTICE      | Für wichtige Ereignisse, die keine Fehler darstellen |
| WARNING     | Für potenziell problematische Situationen |
| ERROR       | Für Fehler, die zwar nicht kritisch sind, aber behoben werden müssen |
| CRITICAL    | Kritische Zustände, die sofortige Aufmerksamkeit erfordern |
| ALERT       | Alarmzustände, die eine sofortige Reaktion erfordern |
| EMERGENCY   | Schwerwiegende Fehler, die das gesamte System betreffen |

Bei der Instanzierung eines LogCommandHandlers kann der Level Protokollierungs, ab wann im jeweiligen LogCommand die Protokollierung stattfinden soll.

---

## Lizenz

Dieses Projekt steht unter der [MIT-Lizenz](LICENSE). Weitere Informationen finden Sie in der Lizenzdatei.

---

Mit der Unterstützung von **mindestens einem Handler** und optionalen **Formattern** bietet `Logger` höchste Flexibilität und Anpassbarkeit für die Protokollierung von Nachrichten in PHP-Projekten – insbesondere in kontextbasierten Architekturen wie **Domain-Driven Design**.

---

## Lieferumfang im Github Repository

- **SourceFile**:
  - src
  - tests
- **Support**:
  - Docker Compose
  - .env
  - pre-commit-hook.sh
  - `Makefile` Einfach `make` in der Konsole aufrufen
- **Dokumentation**:
  - README.md

Der Aufbau des DockerFiles zum erstellen des PHP Images ist etwas umfänglicher gebaut als es für dieses Tool notwendig ist, da das resultierende PHP Image in verschiedenen Jardis Tools eingesetzt wird.

[![Docker Image Version](https://img.shields.io/docker/v/lane4hub/phpcli?sort=semver)](https://hub.docker.com/r/lane4hub/phpcli)

Es wird auch darauf geachtet, das unsere Images so klein wie möglich sind und auf eurem System durch ggf. wiederholtes bauen der Images keine unnötigen Dateien verbleiben.

---

## Jardis Framework

Dieses Tool ist Teil der Entwicklung unseres Domain Driven Design Framework `Jardis` (Just a reliable domain integration system).

`Jardis` besteht aus einer Sammlung hochprofessioneller PHP Software Paketen, die explizit zur effizienten und nachhaltigen Lösung von komplexen Business Anwendungen entwickelt wurden.

Unsere Entwicklung basiert auf definierte Standards wie DDD und PSR mit dem Ziel zur Lieferung höchstmöglicher Qualität funktionaler und nicht funktionaler Anforderungen.

Zur technischen Qualitätssicherung verwenden wir PhpStan Level 8, PhpCS und erzeugen eine vollständige Testabdeckung mit PhpUnit.

Unser Software Pakete erfüllen folgende Qualitäts Attribute:
- Analysierbarkeit
- Anpassungsfähigkeit
- Erweiterbarkeit
- Modularität
- Wartbarkeit
- Testbarkeit
- Skalierbarkeit
- Hohe Leistung

Viel Freude bei der Nutzung!
