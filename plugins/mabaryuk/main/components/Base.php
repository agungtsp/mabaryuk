<?php namespace MabarYuk\Main\Components;

use Auth;
use Cms\Classes\ComponentBase;

/**
 * Base Component
 */
class Base extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Base Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    /**
     * Returns the logged in user, if available
     */
    public function user()
    {
        if (!Auth::check()) {
            return null;
        }

        return Auth::getUser();
    }
}
