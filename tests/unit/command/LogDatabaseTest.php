<?php

namespace Jardis\Logger\Tests\unit\command;

use Jardis\Logger\command\LogDatabase;
use Jardis\Logger\Tests\fixtures\CreateTables;
use PDO;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel as PsrLogLevel;

class LogDatabaseTest extends TestCase
{
    private PDO $pdo;
    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
    }

    public function testLogSuccess(): void
    {
        $schemataSQL = [
            'logContextData' => "CREATE TABLE logContextData (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                context TEXT NOT NULL,
                level TEXT NOT NULL,
                message TEXT NOT NULL,
                data TEXT NOT NULL,
                createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE INDEX idx_context ON logContextData(context);"
        ];

        (new CreateTables())($this->pdo, $schemataSQL);

        $logger = new LogDatabase(PsrLogLevel::INFO, $this->pdo);

        $result = $logger(PsrLogLevel::INFO, 'Test message {key}', ['key' => 'value']);

        $this->assertStringContainsString(
            '"context": "", "level": "info", "message": "Test message value", "data": "{"key":"value"}"',
            $result
        );
    }

    public function testLogAdditionalRecordDataFieldsSuccess(): void
    {
        $schemataSQL = [
            'logContextData' => "CREATE TABLE logContextData (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                context TEXT NOT NULL,
                level TEXT NOT NULL,
                message TEXT NOT NULL,
                data TEXT NOT NULL,
                myOwnContent TEXT NOT NULL,
                createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE INDEX idx_contect ON logContextData(context);"
        ];

        (new CreateTables())($this->pdo, $schemataSQL);

        $logger = new LogDatabase(PsrLogLevel::INFO, $this->pdo);
        $logger->logData()->addLogData('myOwnContent', fn() => 'that is myOwnContent');


        $result = $logger(PsrLogLevel::INFO, 'Test message {myOwnContent}', ['key' => 'value']);

        $this->assertStringContainsString(
            '{ "context": "", "level": "info", "message": "Test message that is myOwnContent", "myOwnContent": "that is myOwnContent", "data": "{"key":"value"}" }
',
            $result
        );
    }
}
