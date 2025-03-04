<?php

namespace Microwin7\PHPUtils\DB;

use PDOStatement;
use stdClass;

interface DriverPDOInterface
{
    public function __construct(?string $database = null, string $table_prefix = '');
    public function __destruct();

    public function getDBH(): \PDO;
    /** Transaction */
    public function beginTransaction(): bool;
    public function inTransaction(): bool;
    public function commit(): bool;
    public function rollback(): bool;

    public function update(string $table): static;
    public function query(string $sql, string $param_type = "", mixed ...$params): static;
    public function getStatementHandler(): PDOStatement;
    public function nextRowset(): static;
    public function rowCount(): int;
    public function result(): array;
    public function value(): mixed;
    public function row(): ?array;
    public function assoc(): ?array;
    public function column(int $column = 0): mixed;
    public function array(): array;
    public function all(): array;
    public function obj(string $class = stdClass::class, array $constructor_args = []): object|null;
    public function objects(string $class = stdClass::class, array $constructor_args = []): array;
    public function id(): int|string|null;
    // Iterator
    public function rewind(): void;
    public function valid(): bool;
    public function key(): int;
    public function current(): array;
    public function next(): void;
    public function unset(): void;
}
