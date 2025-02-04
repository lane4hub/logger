# Jardis Logger
![Build Status](https://github.com/lane4hub/logger/actions/workflows/ci.yml/badge.svg)

Jardis Logger is a PSR-3 extended **flexible and customizable logging system** for PHP, ideal for context-based logging – such as in **Domain-Driven Design (DDD)** – and seamless integration into your projects.

---

**Logger** is a highly powerful and flexible PHP library that supports the logging of messages in a context-based system.

Datalog structures can be customized freely, logging handlers and specific formatters can be added to tailor and format log messages individually.

## Features

- **Logging Levels:** Supports all common levels (Debug, Info, Notice, Warning, Error, Critical, Alert, Emergency).
- **Context-Based Logging:** Each logger instance can be created with a specific context, enabling clearly structured log output – ideal for modeling in **Domain-Driven Design** systems.
- **Customizable Logging Handlers:** Use `LogCommandInterface` for your own implementations to adapt the logging logic to your needs.
- **Customizable Log Record Format:** Extend the columns for log output per LogLevel as needed.
- **Customizable Log User Format:** Extend user data output in logs per LogLevel as required.
- **Formatter for Output:** Use the provided formatters, extend them, or create your own formatter.
- **Log History:** Retrieve a limited number of log histories per log level as needed.

## Available Logger Commands
- LogConsole
- LogFile
- LogDatabase
- LogRedis
- LogErrorLog
- LogSysLog
- LogSlack
- LogStash
- LogNull
- *... extendable as per your requirements!*

## Available Log Data Formats
- LogData Record
  - context (default)
  - loglevel (default)
  - message (default)
  - data (default)

These columns can be expanded as desired.
Log extensions, scalar types, arrays, or callables can be used for population.

For logging into an RDBMS, the columns must be appropriately created.

## Available Extensions for Expanding Log Information
- LogDateTime
- LogClientIp
- LogMemoryUsage
- LogMemoryPeak
- LogUuid
- LogWebRequest
- *... extendable as per your requirements!*

These log extensions can also be used to populate the default `data` field.

## Available Logger Output Formats
- LogLineFormat
- LogJsonFormat
- LogHumanFormat
- *... extendable as per your requirements!*

---

## Using the Jardis Logger

### Example Code to Create a Logger with Two Log Commands

A logger requires an explicit context (e.g., the name of a specific domain, component, or application) and at least one handler that processes the log messages.

```php
use Jardis\Logger\Logger;
use Jardis\Logger\command\LogFile;
use Jardis\Logger\command\LogConsole;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');
$logger->addHandler(new LogConsole(LogLevel::LOG_INFO));
$logger->addHandler(new LogFile(LogLevel::LOG_DEBUG, 'pathToLogFile'));
```

### Example Code for Using the Logger

```php
$logger->debug('This is a debug message', ['extra' => 'debug data']);
$logger->info('This is an info message');
$logger->error('An error occurred!', ['details' => 'error details']);
```

### Example Code for Expanding Data Output

Formatters serve to format log messages differently before output. For example, logs can be output in JSON format.

By default, the `LogLineFormat` is used.

```php
use Jardis\Logger\Logger;
use Jardis\Logger\command\LogConsole;
use Jardis\Logger\servic\format\LogJsonFormat;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');

$logConsole = (new LogConsole(LogLevel::LOG_INFO, 'pathToFile'))->setFormat(new LogJsonFormat());
$logger->addHandler($logConsole);

$logger->info('This is now logged in JSON format!', ['details' => 'info']);
```

### Example Code for Expanding User Data in Logs

In this example, data is added to the `data` field in the log record.

```php
use Jardis\Logger\command\LogFile;
use \Jardis\Logger\service\logData\LogClientIp;
use Jardis\Logger\Logger;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');

$logFile = (new LogFile(LogLevel::LOG_INFO, 'pathToFile'));
$logFile->logData()
    ->addUserLogData('client_ip', new LogClientIp())
    ->addUserLogData('test', fn() => 'value')
    ->addUserLogData('test', 'scalar value');

$logger->addHandler($logFile);
```

### Example Code for Expanding Log Record Data Columns

In this example, data is added as a new column in the log record.

```php
use Jardis\Logger\command\LogFile;
use \Jardis\Logger\service\logData\LogClientIp;
use Jardis\Logger\Logger;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');

$logFile = (new LogFile(LogLevel::LOG_INFO, 'pathToFile'));
$logFile->logData()
    ->addLogData('client_ip', new LogClientIp())
    ->addLogData('test', fn() => 'value')
    ->addLogData('test', 'scalar value');

$logger->addHandler($logFile);
```

**If you want to use LogDatabase, you must first create the corresponding table in your database.**

```sql
CREATE TABLE logContextData (
    id INT AUTO_INCREMENT PRIMARY KEY,
    context TEXT NOT NULL,
    level VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

**You can optionally rename the predefined table and/or add additional fields.**

### Example Code for Using LogDatabase with Default Structure

```php
use Jardis\Logger\Logger;
use Jardis\Logger\command\LogDatabase;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');

$logDatabase = (new LogDatabase(LogLevel::LOG_INFO, $yourPDO));
$logger->addHandler($logDatabase);

$logger->info('Log into database', ['details' => 'data']);
```

---

## Quickstart with Composer

Install the package via Composer:

```sh
composer require lane4hub/logger
```

## Quickstart GitHub

```sh
git clone https://github.com/lane4hub/logger.git
cd logger
```

---

## License

This project is licensed under the [MIT License](LICENSE). See the license file for more details.

---

## Jardis Framework

This tool is part of the development of our Domain-Driven Design framework `Jardis` (Just a reliable domain integration system).

`Jardis` consists of a collection of highly professional PHP software packages developed specifically for efficiently and sustainably solving complex business applications.

Our development is based on defined standards such as DDD and PSR, with the goal of delivering the highest possible quality of functional and non-functional requirements.

To ensure technical quality, we use PhpStan Level 8, PhpCS, and achieve full test coverage with PhpUnit.

Our software packages meet the following quality attributes:
- Analyzability
- Adaptability
- Expandability
- Modularity
- Maintainability
- Testability
- Scalability
- High performance

Enjoy using it!
