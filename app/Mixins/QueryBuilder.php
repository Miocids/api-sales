<?php

namespace App\Mixins;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class QueryBuilder
{

    /**
     * @param array $columns
     * @param string $search
     * @return \Closure
     */
    public function whereLike(array $columns = [], string $search = ""): \Closure
    {
        return function ($columns, $search){
            $this->where(function($query) use ($columns, $search) {
                foreach(Arr::wrap($columns) as $column) {
                    $query->orWhere($column, 'LIKE', "%{$search}%");
                }
            });

            return $this;
        };
	}

}
