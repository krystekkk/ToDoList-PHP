<?php

declare(strict_types=1);

namespace App\Model;

interface ModelInterface
{
    // CRUD
    // READ
    public function list(
        int $pageNumber,
        int $pageSize,
        string $sortBy,
        string $sortOrder
    ): array;

    public function search(
        string $phrase,
        int $pageNumber,
        int $pageSize,
        string $sortBy,
        string $sortOrder
    ): array;

    public function count(): int;

    public function searchCount(string $phrase): int;

    public function get(int $id): array;

    // CREATE
    public function create(array $data): void;

    // UPDATE
    public function edit(int $id, array $data): void;

    // DELETE
    public function delete(int $id): void;
}