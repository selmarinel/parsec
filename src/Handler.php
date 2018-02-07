<?php

namespace Parsec;

use Parsec\Client\Client;
use Parsec\Components\Link;
use Parsec\Components\Matches;
use Parsec\Components\Site;
use Parsec\Crawler\Parser;
use Parsec\Exceptions\ClientParsecException;

class Handler
{
    /** @var Client */
    private $client;
    /** @var Parser */
    private $parser;

    /**
     * Handler constructor.
     */
    public function __construct()
    {
        $this->client = new Client;
        $this->parser = new Parser;
    }

    /**
     * @param $uri
     * @throws ClientParsecException
     */
    public function load($uri)
    {
        $this->client->connect($uri);
        $this->parser->initResponseContent($this->client->getResponse());
    }

    /**
     * @param $href
     * @return Matches
     */
    public function findLinksByHref($href)
    {
        $links = $this->parser->filter('a');
        $result = new Matches();
        foreach ($links as $link) {
            if (mb_strpos($link->getAttribute('href'), $href) !== false) {
                $result->addItem($link);
            }
        }
        return $result;
    }

    /**
     * @param \DOMElement $link
     * @param $text
     * @return bool
     */
    public function checkLinkText(\DOMElement $link, $text)
    {
        return trim($link->textContent) == $text;
    }

    /**
     * @param \DOMElement $link
     * @param $src
     * @return bool
     */
    public function checkLinkImage(\DOMElement $link, $src)
    {
        $img = $link->getElementsByTagName('img');
        $result = false;
        if ($img->length) {
            $result = trim($img->item(0)->getAttribute('src')) == $src;
        }
        return $result;
    }

    /**
     * @param $href
     * @param $anchor
     * @return Matches
     */
    public function check($href, $anchor)
    {
        $links = $this->findLinksByHref($href);
        $result = new Matches();
        foreach ($links->all() as $link) {

            $linkComponent = new Link();
            $linkComponent->setFrom($this->client->getRequest()->getUrl());
            $linkComponent->setTo($href);
            $linkComponent->setAnchor($anchor);
            $linkComponent->setStatus(Link::ANCHOR_MISMATCH);

            if ($this->checkLinkText($link, $anchor) || $this->checkLinkImage($link, $anchor)) {
                $linkComponent->setStatus(Link::LIVE);
            }
            $result->addItem($linkComponent);

        }
        return $result;
    }

    public static function report(Matches $matches)
    {
        $report = new Matches();
        foreach ($matches->all() as $match) {
            if ($match instanceof Site) {

                if (!$match->getLinks()) {
                    $report->addItem([Link::NOT_FOUND, $match->getUrl()]);
                    continue;
                }
                $isLive = false;
                foreach ($match->getLinks() as $link) {
                    /** @var Link $link */
                    if ($link->getStatus() === Link::LIVE) {
                        $isLive = true;
                    }
                }
                $report->addItem([
                    ($isLive) ? Link::LIVE : Link::ANCHOR_MISMATCH,
                    $match->getUrl()
                ]);
            }
        }
        return $report->all();
    }

}