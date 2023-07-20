<?php

namespace App\Repositories;

namespace App\Repositories;

use App\Exceptions\UserDoesNotExistException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class AbstractRepository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * @throws UserDoesNotExistException
     */
    public function getByIdOrFail(int $id): Model
    {
        try
        {
            return $this->model->findOrFail($id);
        }
        catch (ModelNotFoundException $exception)
        {
            throw new UserDoesNotExistException("User with ID $id does not exist.", 404, $exception);
        }
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Model
    {
        $record = $this->find($id);

        if ($record)
        {
            $record->update($data);
            return $record;
        }

        return null;
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);

        if ($record)
        {
            $record->delete();
            return true;
        }

        return false;
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }
}

