<?php

declare(strict_types=1);

namespace Jardis\Logger\command;

use PDO;

/**
 * LogDatabase is responsible for persisting log entries in a specific database table.
 * It extends LogCommand to provide context and log level functionality, while using a
 * repository to handle database interactions.
 */
class LogDatabase extends LogCommand
{
    private PDO $pdo;
    private string $logTable;

    /**
     * Constructor for initializing the logging system.
     *
     * @param string $logLevel The log level to set for this instance.
     * @param PDO $pdo A PDO instance for database connections.
     * @param string|null $logTable The name of the log table, defaults to 'logContextData' if not provided.
     * @return void
     */
    public function __construct(string $logLevel, PDO $pdo, ?string $logTable = null)
    {
        $this->pdo = $pdo;
        $this->logTable = $logTable ?? 'logContextData';

        parent::__construct($logLevel);
    }

    protected function log(string $logMessage, array $logData): bool
    {
        $logData['data'] = json_encode($logData['data'] ?? []);

        $statement = $this->pdo->prepare(
            $this->buildQuery(
                array_keys($logData)
            )
        );

        return $statement->execute(array_values($logData));
    }

    /** @param array<int, string> $fields  */
    protected function buildQuery(array $fields): string
    {
        $placeholders = array_map(fn($column) => "?", $fields);

        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->logTable,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
    }
}
