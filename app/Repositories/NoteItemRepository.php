<?php namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\{NoteItem};

class NoteItemRepository extends BaseRepository
{

    /**
     * Construct NoteItemRepository class
     */
    public function __construct()
    {
        parent::__construct(new NoteItem);
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
                    return $subQuery->whereHas('item', function($subQuery){
                        return $subQuery->whereLike(["name", "sku","price"], \request()->string("text"));
                    })
                    ->orWhereHas('note', function($subQuery){
                        return $subQuery->whereHas('customer', function($subSubQuery){
                            return $subSubQuery->whereLike(['name','email'], \request()->string("text")) ;
                        });
                    });
                });
            })
            ->when(\request("status"), function ($query){
                return $query->where("status",\request()->boolean("status"));
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
            "note_id" => $attributes["note_id"],
            "item_id" => $attributes["item_id"],
            "quantity" => $attributes["quantity"],
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

    public function getCurrencies($totalCop):mixed
    {
        $curl = curl_init();
            $currencies = ["USD","EUR"];

            foreach ($currencies as $currency) {
                curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.apilayer.com/exchangerates_data/convert?to={$currency}&from=COP&amount={$totalCop}",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: text/plain",
                    "apikey: jqYTnjlPTqELZQ3ZiCvSGYnvC77Ojvfs"
                ),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_CUSTOMREQUEST => "GET"
                ));

                $r = curl_exec($curl);
                $totals[] = $r;
            }

            curl_close($curl);

            $decodedUsd = json_decode($totals[0], true);
            $decodedEur = json_decode($totals[1], true);

            $totalCurrencies = [
                "usd" => $decodedUsd["result"],
                "eur" => $decodedEur["result"],
            ];

            return $totalCurrencies;
    }

}