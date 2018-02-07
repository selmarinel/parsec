<?php

namespace Parsec\Crawler;

use JonnyW\PhantomJs\Http\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class Parser
{
    /** @var Crawler */
    private $crawler;

    private $content;

    public function __construct()
    {
        $this->crawler = new Crawler();
    }

    public function getCrawler()
    {
        return $this->crawler;
    }

    public function initResponseContent(ResponseInterface $response)
    {
        $this->content = $this->crawler->addHtmlContent($response->getContent());
    }

    /**
     * @param $filter
     * @return Crawler
     */
    public function filter($filter)
    {
        return $this->crawler->filter($filter);
    }
}