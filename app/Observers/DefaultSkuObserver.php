<?php

namespace App\Observers;

use Illuminate\Support\Facades\{DB};

class DefaultSkuObserver
{
    /**
     * @param $model
     * @return void
     */
    public function creating($model): void
    {
        if ($userId = \auth()->user()?->getKey()){
            $total = DB::table($model->getTable())
            ->where(["created_by" => $userId])
            ->whereNull('deleted_at')
            ->orWhereNotNull('deleted_at')
            ->count();
            $keys  = $this->_getKeyByModel($total);
            $year  = now()->year;
            switch ($model::class){
                case "App\Models\Item":
                    $prefix = "IT{$userId}";
                    if (!$model->sku)
                        $model->sku = "{$prefix}{$year}/{$keys}";
                    break;

                default:

                    break;
            }
        }

    }

    /**
     * @param $total
     * @return string
     */
    private function _getKeyByModel($total): string
    {
        $qty   = ($total + 1);
        $keys  = $qty;
        if ($qty < 10)  $keys = "000{$qty}";
        if ($qty > 9 && $qty < 100)  $keys = "00{$qty}";
        if ($qty > 99 && $qty < 1000)  $keys = "0{$qty}";

        return $keys;
    }

}
