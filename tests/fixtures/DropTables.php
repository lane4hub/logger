<?php

namespace Jardis\Logger\Tests\fixtures;

use PDO;
use PDOException;

class DropTables
{
    /**
     * @param PDO $pdo
     * @param array<string, string> $schemataSQL
     * @return void
     */
    public function __invoke(PDO $pdo, array $schemataSQL): void
    {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            foreach ($schemataSQL as $table => $sql) {
                $pdo->exec("DROP TABLE IF EXISTS {$table};");
            }

        } catch (PDOException $e) {
            echo "Error while dropping tables: " . $e->getMessage();
        }
    }
}
