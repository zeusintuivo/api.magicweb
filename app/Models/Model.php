<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Model
 * @package App\Models
 */
class Model extends EloquentModel
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that aren't mass assignable
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Counting models utilizing scope filter
     * Persist eventual search query on all models
     * @return int
     */
    public static function filterCount()
    {
        return self::filter(request(['q']))->count();
    }

    /**
     * Extend collection for all elloquent models
     *
     * @param  array $models
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return parent::newCollection($models);
        // return new ModelCollection($models);
    }
}
