<?php namespace App\Services;

use Illuminate\Http\{Response};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\{Collection, Str };
use App\Repositories\{NoteItemRepository, NoteRepository, ItemRepository};
use Illuminate\Support\Facades\{ Cache, DB, Storage };

class NoteItemService extends NoteItemRepository
{
    /**
     * Store a newly created resource in storage.
     *
     * @return Model|null
     * @throws \Exception
     */
    public function store(): ?Model
    {
        DB::beginTransaction();
        try {
            $item = (new ItemRepository())->getById(\request("item"));
            $totalCop = (\request("quantity") * $item?->price);
            $date = \now()->toDateString();

            $currencies = $this->getCurrencies($totalCop);

            $note = (new NoteRepository())->getByCustomerAndDate(\request("customer"), $date);

            $notePayload = [
                "customer_id"   => \request("customer"),
                "date"          => $date,
                "total"         => ($note)? ($note?->total + $totalCop) : $totalCop
            ];
            $saveNote = (new NoteRepository())->saveRepository($notePayload);

            $path = \request("customer")."/images";
            $filePath = \request()->file("file")->store($path,"public");
            $payload = \request()->merge([
                "note_id"   => $saveNote?->getKey(),
                "item_id"   => \request("item"),
                "total"     => $totalCop,
                "total_usd" => $currencies["usd"],
                "total_eur" => $currencies["eur"],
                "attach"    => $filePath,
            ])->except("customer","item","file");
            $response = $this->saveRepository($payload);

            DB::commit();

            return $response;

        } catch (\Throwable $e) {
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            \Log::error($error);
            DB::rollback();

            throw new \Exception($e->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $id
     * @return Model|null
     * @throws \Exception
     */
    public function update(string $id): ?Model
    {
        DB::beginTransaction();
        try {
            $item = (new ItemRepository())->getById(\request("item"));
            $totalCop = (\request("quantity") * $item?->price);
            $date = \now()->toDateString();

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
            $notePayload = [
                "customer_id"   => \request("customer"),
                "date"          => $date,
                "total"         => $totalCop
            ];
            $note = (new NoteRepository())->saveRepository($notePayload);

            $payload = \request()->merge([
                "note_id"   => $note?->getKey(),
                "item_id"   => \request("item"),
                "total"     => $totalCop,
                "total_usd" => $decodedUsd["result"],
                "total_eur" => $decodedEur["result"],
            ])->except("customer","item");
            $response = $this->saveRepository($payload);
             $response = $this->updateRepository(
                $payload,
                $id
            );

            DB::commit();

            return $response;

        } catch (\Throwable $e) {
            $error = $e->getMessage() . " " . $e->getLine() . " " . $e->getFile();
            \Log::error($error);
            DB::rollback();

            throw new \Exception($e->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

}