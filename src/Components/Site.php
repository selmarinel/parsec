<?php

namespace Parsec\Components;


class Site
{

    private $links = [];

    private $url;

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function addLink(Link $link)
    {
        $this->links[] = $link;
    }

    public function getLinks()
    {
        return $this->links;
    }
}