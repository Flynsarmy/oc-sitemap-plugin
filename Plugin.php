<?php namespace Flynsarmy\Sitemap;

use Event;
use System\Classes\PluginBase;
use System\Classes\PluginManager;

/**
 * Sitemap Plugin Information File
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
            'name'        => 'Sitemap',
            'description' => 'Generates and displays your sitemap',
            'author'      => 'Flynsarmy',
            'icon'        => 'icon-sitemap'
        ];
    }

    public function registerComponents()
    {
        return [
            '\Flynsarmy\Sitemap\Components\Sitemap' => 'sitemap'
        ];
    }

    public function boot()
    {
        if ( PluginManager::instance()->hasPlugin('RainLab.Blog') )
        {
            Event::listen('flynsarmy.sitemap.beforeGetDetails', function($generator, $page) {
                if ( !$page->hasComponent('RainLab\Blog\Components\Post') )
                    return;

                $posts = \RainLab\Blog\Models\Post::get();
            });
        }
    }
}
