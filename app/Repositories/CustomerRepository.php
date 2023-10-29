<?php namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\{Customer};

class CustomerRepository extends BaseRepository
{

    /**
     * Construct CustomerRepository class
     */
    public function __construct()
    {
        parent::__construct(new Customer);
    }

    /**
     * @return mixed
     */
    public function getAllQuery(): mixed
    {
        return $this->getRepository()
            ->query()
            ->when(\auth()->user(), function ($query){
                return $query->whereCreatedBy(\auth()->user()?->getKey());
            })
            ->when(\request("text"), function ($query){
                return $query->where(function ($subQuery){
                    return $subQuery->whereLike(["email","name"], \request()->string("text"));
                });
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
            "name"  => $attributes["name"],
            "email" => $attributes["email"],
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

}