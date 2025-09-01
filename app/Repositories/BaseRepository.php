<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find record by ID
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find record by ID or throw exception
     */
    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->update($data);
    }

    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        $record = $this->find($id);
        if (!$record) {
            return false;
        }
        return $record->delete();
    }

    /**
     * Get paginated records
     */
    public function paginate(int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Get records with conditions
     */
    public function where(string $column, $value): Collection
    {
        return $this->model->where($column, $value)->get();
    }

    /**
     * Get first record with conditions
     */
    public function whereFirst(string $column, $value): ?Model
    {
        return $this->model->where($column, $value)->first();
    }

    /**
     * Get records with multiple conditions
     */
    public function whereMultiple(array $conditions): Collection
    {
        $query = $this->model->newQuery();
        foreach ($conditions as $column => $value) {
            $query->where($column, $value);
        }
        return $query->get();
    }

    /**
     * Count records
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Count records with conditions
     */
    public function countWhere(string $column, $value): int
    {
        return $this->model->where($column, $value)->count();
    }

    /**
     * Check if record exists
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
