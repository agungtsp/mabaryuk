<?php namespace MabarYuk\Master\Models;

use BackendAuth;
use Model;
use Schema;

/**
 * Base Model
 */
class BaseModel extends Model
{
    use \October\Rain\Database\Traits\SoftDelete;

    /**
     * Model Bootstrap
     *
     * @return void
     */
    protected static function boot() {
        parent::boot();

        // Event on Create
        self::creating(function ($model) {
            if (BackendAuth::check() && Schema::hasColumn($model->getTable(), 'created_by'))
                $model->created_by = BackendAuth::getUser()->id;
        });

        // Event on Update
        self::updating(function ($model) {
            if (BackendAuth::check() && Schema::hasColumn($model->getTable(), 'updated_by') && ! $model->deleted_by)
                $model->updated_by = BackendAuth::getUser()->id;
        });

        // Event on Delete
        self::deleting(function ($model) {
            if (BackendAuth::check() && Schema::hasColumn($model->getTable(), 'deleted_by'))
                $model->deleted_by = BackendAuth::getUser()->id;

            // Should do this for deleting
            $model->timestamps = false; // Updated_at will not update
            $model->save();
        });
    }
}