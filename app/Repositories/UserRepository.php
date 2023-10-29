<?php namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\{User};

class UserRepository extends BaseRepository
{

    /**
     * Construct UserRepository class
     */
    public function __construct()
    {
        parent::__construct(new User);
    }

    /**
     * @return mixed
     */
    public function getAllQuery(): mixed
    {
        return $this->getRepository()
            ->query()
            ->whereNotIn('email', [
                "jorge.martinez@itsolutionsengly.com",
                "administrador@dominio.com",
            ])
            ->when(\request()->header("Company"), function ($query){
                return $query->whereRelation("companies","company_id","=",\request()->header("Company"));
            })
            ->when(\request("status"), function ($query){
                return $query->where("status",\request()->boolean("status"));
            })
            ->when(\request("text"), function ($query){
                return $query->where(function ($subQuery){
                    return $subQuery->whereLike(["name"], \request()->string("text")->title())
                        ->orWhere(function ($subQuery){
                            return $subQuery->whereLike(["email"], \request()->string("text")->title());
                        });
                });
            })
            ->latest()
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
            "email" => $attributes["email"]
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

    /**
     * Search for a user if it does not exist create the resource
     *
     * @param array $attribute
     * @return Builder|Model
     */
    public function getDefaultOrSaveRepository(array $attribute): Model|Builder
    {
        return $this->getRepository()->query()->where($attribute)->firstOr(function () use ($attribute){
            $data = [
                "email"      =>  "administrador@dominio.com",
                "name"       =>  "Administrador Sistema",
                "username"   =>  "administrador",
                "password"   =>  bcrypt("Administrador-001"),
            ];
            return $this->save([
                "email" => $data["email"]
            ],$data);
        });
    }


}
