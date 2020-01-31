<?php

namespace Flynsarmy\Sitemap\Components;

use Cms\Classes\ComponentBase;
use Flynsarmy\Sitemap\Classes\Generator;

class Sitemap extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Sitemap',
            'description' => 'Displays your sitemap',
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    /**
     * Executed when this component is bound to a page or layout, part of
     * the page life cycle.
     */
    public function onRun()
    {
        return Generator::instance()->generate();
        // return \Response::make('hiyaaaaa')->header('Content-type', 'text/csv');
    }
}
