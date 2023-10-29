<?php

namespace App\Observers;

/**
 * @property mixed|null $user_id
 */
class CreatedByObserver
{
    public function __construct()
    {
        $this->user_id = \request()->user()?->getKey();
    }
    /**
     * @param $model
     * @return void
     */
    public function creating($model): void
    {
        $model->created_by = $this->user_id;
    }
    /**
     * @param $model
     * @return void
     */
    public function created($model): void
    {
        $model->created_by = $this->user_id;
    }

}
