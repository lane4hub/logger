<?php

namespace Jardis\Logger\Tests\fixtures;

use PDO;
use PDOException;

class CreateTables
{
    /**
     * @param PDO $pdo
     * @param array<string, string> $schemataSQL
     * @return void
     */
    public function __invoke(PDO $pdo, array $schemataSQL): void
    {
        try {
            (new DropTables)($pdo, $schemataSQL);

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            foreach ($schemataSQL as $sql) {
                $pdo->exec($sql);
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
