<?php namespace MabarYuk\Master;

use Backend;
use System\Classes\PluginBase;

/**
 * Master Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Master',
            'description' => 'Talent Hero master management',
            'author'      => 'MabarYuk',
            'icon'        => 'icon-database'
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
        return [
            'MabarYuk\Master\Components\Master' => 'master',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'mabaryuk.master.access_locations' => [
                'tab' => 'Master',
                'label' => 'Manage master locations'
            ],
            'mabaryuk.master.access_businesstypes' => [
                'tab' => 'Master',
                'label' => 'Manage master businesstypes'
            ],
            'mabaryuk.master.access_surahs' => [
                'tab' => 'Master',
                'label' => 'Manage master surah'
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
        return [
            'master' => [
                'label'       => 'Master',
                'url'         => Backend::url('mabaryuk/master/countries'),
                'icon'        => 'icon-database',
                'permissions' => ['mabaryuk.master.*'],
                'order'       => 500,
                'sideMenu' => [
                    'locations' => [
                        'label' => 'Location',
                        'icon' => 'icon-map-marker',
                        'url' => Backend::url('mabaryuk/master/locations'),
                        'permissions' => ['mabaryuk.master.access_locations'],
                    ],
                    'businesstypes' => [
                        'label' => 'Business Type',
                        'icon' => 'icon-industry',
                        'url' => Backend::url('mabaryuk/master/businesstypes'),
                        'permissions' => ['mabaryuk.master.access_businesstypes'],
                    ],
                    'surahs' => [
                        'label' => 'Surah',
                        'icon' => 'icon-book',
                        'url' => Backend::url('mabaryuk/master/surahs'),
                        'permissions' => ['mabaryuk.master.access_surahs'],
                    ],
                ]
            ],
        ];
    }
}
