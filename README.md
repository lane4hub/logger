# Jardis Logger
![Build Status](https://github.com/lane4hub/logger/actions/workflows/ci.yml/badge.svg)

Jardis Logger is a PSR-3 extended **flexible and customizable logging system** for PHP, ideal for context-based logging – such as in **Domain-Driven Design (DDD)** – and seamless integration into your projects.

---

**Logger** is a very powerful and flexible PHP library that supports the logging of messages in a context-based system.

Log data structures can be customized freely, logging handlers and specific formatters can be added to individually design and format log messages.

## Features

- **Log Levels:** Supports all common levels (Debug, Info, Notice, Warning, Error, Critical, Alert, Emergency).
- **Context-Based Logging:** Each logger instance can be created with a specific context, enabling a clearly structured log output – ideal for modeling in **Domain-Driven Design** systems.
- **Customizable Logging Handlers:** Use any implementation of `LogCommandInterface` to tailor the logging logic to your needs.
- **Customizable Log Data:** Extend the log output data per log level as needed.
- **Output Formatters:** Ensure logs can be individually formatted before output.
- **Log History:** Retrieve a limited log history per log level as required.

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
- *... extendable as needed!*

## Available Log Data Formats
- LogData (Default with Context, Log Level, and Message)
  - LogDateTime
  - LogClientIp
  - LogMemoryUsage
  - LogMemoryPeak
  - LogUuid
  - LogWebRequest
  - *... extendable as needed!*

## Available Logger Output Formats
- LogLineFormat
- LogJsonFormat
- LogHumanFormat
- *... extendable as needed!*

---

## Using Jardis Logger

### Example: Creating a Logger with Two Log Commands

A logger requires an explicit context (e.g., the name of a specific domain, component, or application) and at least one handler to process log messages.

```php
use Jardis\Logger\Logger;
use Jardis\Logger\command\LogFile;
use Jardis\Logger\command\LogConsole;
use Jardis\Logger\command\LoggerInterface;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');
$logger->addHandler(new LogConsole(LogLevel::LOG_INFO));
$logger->addHandler(new LogFile(LogLevel::LOG_DEBUG, 'pathToLogFile'));
```

### Example: Logging Messages

```php
$logger->debug('This is a debug message', ['extra' => 'Debug data']);
$logger->info('This is an info message');
$logger->error('An error occurred!', ['details' => 'Error details']);
```

### Example: Extending Log Output Data

Formatters are used to format log messages before output. For example, logs can be output in JSON format.

By default, a text logger is used.

```php
use Jardis\Logger\command\LogConsole;
use Jardis\Logger\command\LoggerInterface;
use Jardis\Logger\Logger;
use Jardis\Logger\service\format\LogJsonFormat;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');

$logConsole = (new LogConsole(LogLevel::LOG_INFO, 'pathToFile'))->setFormat(new LogJsonFormat());
$logger->addHandler($logConsole);

$logger->info('This will now be output in JSON format!', ['details' => 'info']);
```

### Example: Changing an Output Formatter per Log Command and Extending Log Data (Optional)

Formatters are used to format log messages before output. For example, logs can be output in JSON format.

By default, a text logger is used.

```php
use Jardis\Logger\command\LogFile;
use Jardis\Logger\command\LogSlack;
use Jardis\Logger\command\LoggerInterface;
use Jardis\Logger\service\format\LogJsonFormat;
use Jardis\Logger\service\format\LogHumanFormat;
use Jardis\Logger\service\logData\LogWebRequestData;
use Jardis\Logger\Logger;
use Psr\Log\LogLevel;

$logger = new Logger('myDomain');

$logFile = (new LogFile(LogLevel::LOG_INFO, 'pathToFile'))->setFormat(new LogJsonFormat());
$logSlack = (new LogSlack(LogLevel::LOG_ERROR, 'webHookUrl'))->setFormat(new LogHumanFormat());
$logger->addHandler($logFile);
$logger->addHandler($logSlack);
$logger->addLogData(LogLevel::LOG_ERROR, new LogWebRequestData());
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

**If you want to use LogDatabase, you must first create a corresponding table in your database.**

```sql
CREATE TABLE logContextData (
    id INT AUTO_INCREMENT PRIMARY KEY,
    context TEXT NOT NULL,
    level VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

---

## License

This project is licensed under the [MIT License](LICENSE). For more details, see the license file.

---

## Jardis Framework

This tool is part of the development of our **Domain-Driven Design Framework** `Jardis` (Just a reliable domain integration system).

`Jardis` consists of a collection of highly professional PHP software packages explicitly developed for efficient and sustainable solutions for complex business applications.

Our development is based on defined standards such as DDD and PSR, aiming to deliver the highest possible quality for functional and non-functional requirements.

To ensure technical quality, we use PhpStan Level 8, PhpCS, and generate full test coverage with PhpUnit.

Our software packages fulfill the following quality attributes:
- Analyzability
- Adaptability
- Extensibility
- Modularity
- Maintainability
- Testability
- Scalability
- High Performance

Enjoy using it!
