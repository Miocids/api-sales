<?php namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\{Note};

class NoteRepository extends BaseRepository
{

    /**
     * Construct NoteRepository class
     */
    public function __construct()
    {
        parent::__construct(new Note);
    }

    /**
     * @return mixed
     */
    public function getAllQuery(): mixed
    {
        return $this->getRepository()
            ->query()
            ->when(\request("text"), function ($query){
                return $query->where(function ($subQuery){
                    return $subQuery->whereLike(["date"], \request()->string("text"))
                    ->orWhereHas('customer', function($subQuery){
                        return $subQuery->whereLike(['name','email'], request()->string("text"));
                    });
                });
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
            "customer_id" => $attributes["customer_id"],
            "date"        => $attributes["date"],
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