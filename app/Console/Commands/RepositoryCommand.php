<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class link to model';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $repositoryName = $this->argument('repository');
        $pathRepository = "app/Repositories";

        $this->components->task("Creating repository {$pathRepository}/{$repositoryName}.php", function() use ($pathRepository,$repositoryName) {

            if (!File::exists($pathRepository))
                File::makeDirectory($pathRepository,0775,true);

            if (!File::exists("{$pathRepository}/Interfaces"))
                File::makeDirectory("{$pathRepository}/Interfaces",0775,true);

            if (!File::exists("{$pathRepository}/Interfaces/RepositoryInterface.php")){
                $schema = $this->schemaFileRepositoryInterface();
                File::put("{$pathRepository}/Interfaces/RepositoryInterface.php",$schema);
            }
            if (!File::exists("{$pathRepository}/BaseRepository.php")){
                $schemaExtends = $this->schemaFileRepositoryExtends();
                File::put("{$pathRepository}/BaseRepository.php",$schemaExtends);
            }
            if(!File::exists("{$pathRepository}/{$repositoryName}.php")){
                $schema = $this->schemaFileRepository($repositoryName);
                File::put("{$pathRepository}/{$repositoryName}.php",$schema);
            }

        });

    }

    /**
     * @return string
     */
    private function schemaFileRepositoryInterface(): string
    {
        return
            '<?php namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface RepositoryInterface
{
   public function all(): Collection;

    public function save(array $data, array $attributes);

    public function getOrSave(array $data, array $attributes);

    public function delete($id);

    public function getById($id,array $columns);

    public function filterIn(string $field, array $data);

    public function getBy(array $attributes, array $columns);

    public function filterBy(array $data, array $columns): Collection;

}';

    }

    /**
     * @return string
     */
    private function schemaFileRepositoryExtends(): string
    {
        return
            '<?php namespace App\Repositories;

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

    public function getOrSave(array $data, array $attributes): ?Model
    {
        // TODO: Implement create() method.
        return $this->_repository->query()->firstOrCreate($data,$attributes);
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

}';
    }

    /**
     * @param string $repositoryName
     * @return string
     */
    private function schemaFileRepository(string $repositoryName): string
    {
        $entityName = Str::of($repositoryName)->replace("Repository","");
        return
            '<?php namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\{'.$entityName.'};

class '.$repositoryName.' extends BaseRepository
{

    /**
     * Construct '.$repositoryName.' class
     */
    public function __construct()
    {
        parent::__construct(new '.$entityName.');
    }

    /**
     * @return mixed
     */
    public function getAllQuery(): mixed
    {
        return $this->getRepository()
            ->query()
            ->when(\request("key"), function ($query){
                return $query->where("key",\request("key"));
            })
            ->when(\request("status"), function ($query){
                return $query->where("status",\request()->boolean("status"));
            })
             ->when(\request("page"), function ($query){
                return $query->paginate(\request("to"));
            },function ($query){
                return $query->get();
            });
    }

    /**
     * @param array $attributes
     * @return Model|null
     */
    public function saveRepository(array $attributes): ?Model
    {
        return $this->save([
            "key" => $attributes["key"]
        ],$attributes);
    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model|null
     */
    public function updateRepository(array $attributes,string $id): ?Model
    {
        return $this->save([
            "id" => $id
        ],$attributes);
    }

}';
    }
}
