<?php namespace MabarYuk\Main;

use Backend;
use System\Classes\PluginBase;

/**
 * Main Plugin Information File
 */
class Plugin extends PluginBase
{
	public $require = ['MabarYuk.Master', 'RainLab.User'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Main',
            'description' => 'No description provided yet...',
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

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'MabarYuk\Main\Components\MyComponent' => 'myComponent',
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
            'mabaryuk.main.some_permission' => [
                'tab' => 'Main',
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
            'main' => [
                'label'       => 'Main',
                'url'         => Backend::url('mabaryuk/main/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['mabaryuk.main.*'],
                'order'       => 500,
            ],
        ];
    }
}
