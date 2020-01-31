<?php

namespace Flynsarmy\Sitemap\Classes;

use Cache;
use Carbon\Carbon;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use DOMDocument;
use Event;

class Generator
{
    use \October\Rain\Support\Traits\Singleton;

    public $maxAge = 30; // Max age of sitemap xml in minutes
    protected $pages; // Array of Cms\Classes\Page

    protected $xml; // DOMDocument
    protected $urlset; // DOMElement

    public function __construct()
    {
        $theme = Theme::getEditTheme();
        $this->pages = Page::listInTheme($theme, true);
    }

    public function generate()
    {
        // Do we have a cache?
        // if ( ($xml = Cache::get('flynsarmy.sitemap.xml', 'flynsarmy.sitemap')) )
        // 	return $xml;

        $this->xml = new DOMDocument();
        $this->xml->encoding = 'UTF-8';

        $this->urlset = $this->xml->createElement('urlset');
        $this->urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $this->urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->urlset->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

        foreach ($this->pages as $page) {
            $this->add_page($page);
        }

        $this->xml->appendChild($this->urlset);

        // Cache::put('flynsarmy.sitemap.xml', $xml, $this->maxAge);

        exit($this->xml->saveXML());

        return $this->xml;
    }

    public function add_page(Page $page)
    {
        $details = array_merge([
            'url' => Page::url($page->fileName),
            // @TODO This should be update time, not create time
            'lastModified' => Carbon::createFromTimeStamp($page->mtime)->toIso8601String(),
            // @TODO Page-specific change frequency/priority
            'changeFreq' => 'daily',
            'priority'   => '1.0',
        ], (array) Event::fire('flynsarmy.sitemap.beforeGetDetails', [$this, $page], true));

        // More than one URL set for this page?
        if (is_array(reset($details))) {
            foreach ($details as $detail) {
                $this->add_url_element($detail['url'], $detail['lastModified'], $detail['changeFreq'], $detail['priority']);
            }
        } else {
            $this->add_url_element($details['url'], $details['lastModified'], $details['changeFreq'], $details['priority']);
        }
    }

    public function add_url_element($url, $lastmod, $changefreq, $priority)
    {
        $node = $this->xml->createElement('url');

        $node->appendChild($this->xml->createElement('loc', $url));
        $node->appendChild($this->xml->createElement('lastmod', $lastmod));
        $node->appendChild($this->xml->createElement('changefreq', $changefreq));
        $node->appendChild($this->xml->createElement('priority', $priority));

        $this->urlset->appendChild($node);
        // $this->url_count++;
    }
}
