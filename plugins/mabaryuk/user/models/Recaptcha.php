<?php namespace MabarYuk\User\Models;

use Model;

/**
 * Recaptcha Model
 */
class Recaptcha extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var array Behaviors implemented by this model.
     */
    public $implement = [
        \System\Behaviors\SettingsModel::class
    ];

    public $settingsCode = 'recaptcha_settings';
    public $settingsFields = 'fields.yaml';

    public $rules = [
        'site_key' => 'required',
        'secret_key' => 'required'
    ];
}
