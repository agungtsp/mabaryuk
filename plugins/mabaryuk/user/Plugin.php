<?php namespace MabarYuk\User;

use Backend;
use System\Classes\PluginBase;
use Rainlab\User\Models\User;

/**
 * User Plugin Information File
 */
class Plugin extends PluginBase
{
	public $require = ['MabarYuk.Main'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Talent Hero\'s user',
            'description' => 'Talent Hero\'s user, Extension version from Rainlab.User',
            'author'      => 'MabarYuk',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        //
        // Extend RainLab.User
        //
        User::extend(function($model) {

			$model->implement[] = 'MabarYuk.User.Behaviors.MabarYukUser';
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'MabarYuk\User\Components\Account'         => 'heroAccount',
            'MabarYuk\User\Components\ResetPassword'   => 'heroResetPassword',
            'MabarYuk\User\Components\UserApplication' => 'userApplication',
            'MabarYuk\User\Components\UserTalent'      => 'userTalent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'mabaryuk.user.some_permission' => [
                'tab' => 'User',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'user' => [
                'label'       => 'User',
                'url'         => Backend::url('mabaryuk/user/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['mabaryuk.user.*'],
                'order'       => 500,
            ],
        ];
    }

    /**
     * @overriding
     *
     * @return array
     */
    public function registerSettings() {
        return [
            'recaptcha' => [
                'label' => 'Recaptcha',
                'description' => 'Recapthca configuration',
                'category' => 'Recapthca',
                'icon' => 'icon-cog',
                'permissions' => ['mabaryuk.user.*'],
                'order'       => 500,
                'class'       => 'MabarYuk\User\Models\Recaptcha'
            ]
        ];
    }
}
