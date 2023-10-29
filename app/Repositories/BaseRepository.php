<?php namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\Interfaces\{RepositoryInterface};

class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model table
     */
    private Model $_repository;

    public function __construct(Model $model)
    {
        $this->setRepository($model);
    }

    public function all(): Collection
    {
        // TODO: Implement all() method.
        return $this->_repository->query()->get();
    }

    public function save(array $data, array $attributes): ?Model
    {
        // TODO: Implement create() method.
        return $this->_repository->query()->updateOrCreate($data,$attributes);
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
        return $this->_repository->query()->find($id)->delete();
    }

    public function getById($id, array $columns = ["*"]): ?Model
    {
        // TODO: Implement find() method.
        return $this->_repository->query()->find($id, $columns);
    }

    public function filterIn(string $field, array $data): ?Collection
    {
        // TODO: Implement filterIn() method.
        return $this->_repository->query()->whereIn($field, $data)->get();
    }

    public function getBy(array $attributes, array $columns = ["*"]): ?Model
    {
        // TODO: Implement filterIn() method.
        return $this->_repository->query()->where($attributes)->first($columns);
    }

    public function filterBy(array $data, array $columns = ["*"]): Collection
    {
        // TODO: Implement filters() method.
        return $this->_repository->query()->where($data)->get($columns);
    }

    protected function setRepository(Model $model): BaseRepository
    {
        // TODO: Implement setRepository() method.
        $this->_repository = $model;
        return $this;
    }

    protected function getRepository(): ?Model
    {
        // TODO: Implement getRepository() method.
        return $this->_repository;
    }

}