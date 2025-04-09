<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritdoc
     */
    public function getAll(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id, array $columns = ['*'], array $relations = [], array $appends = []): ?Model
    {
        $model = $this->model->select($columns)->with($relations)->findOrFail($id);

        if (!empty($appends)) {
            $model->append($appends);
        }

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function update(int $id, array $data): bool
    {
        $model = $this->getById($id);
        return $model->update($data);
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id): bool
    {
        return $this->getById($id)->delete();
    }

    /**
     * @inheritdoc
     */
    public function findByCriteria(array $criteria, array $columns = ['*'], array $relations = []): Collection
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            if (is_array($value)) {
                list($field, $operator, $search) = $value;
                $query->where($field, $operator, $search);
            } else {
                $query->where($key, '=', $value);
            }
        }

        return $query->select($columns)->with($relations)->get();
    }
}
