<?php

namespace Parsec\Driver\PhantomJs;

use Parsec\Boot;
use Parsec\Components\Matches;
use Parsec\DOMEntities\ImageDomComponent;
use Parsec\DOMEntities\LinkDOMComponent;
use Parsec\Driver\DriverInterface;
use Parsec\Exceptions\ClientParsecException;
use Parsec\Exceptions\PhantomEngineException;
use Parsec\Exceptions\PhantomParsecException;
use Symfony\Component\DomCrawler\Crawler;
use JonnyW\PhantomJs\Client as PhantomClient;

class Driver implements DriverInterface
{
    /** @var Crawler */
    private $crawler;
    /** @var PhantomClient */
    private $client;

    /**
     * Driver constructor.
     * @throws PhantomEngineException
     * @throws PhantomParsecException
     */
    public function __construct()
    {
        $this->crawler = new Crawler();
        $this->client = PhantomClient::getInstance();

        $boot = (new Boot())();
        $path = '';
        if ($boot->phantomjs) {
            $path = PARSEC_ROOT_PATH . $boot->phantomjs['path'];
        }

        if (!file_exists($path)) {
            throw new PhantomParsecException;
        }
        try {
            $this->client->getEngine()->setPath($path);
        } catch (\Exception $exception) {
            throw new PhantomEngineException;
        }

    }

    public function close()
    {
        $this->crawler = new Crawler();
    }

    /**
     * @param $url
     * @param int $connectionTimeout
     * @throws ClientParsecException
     */
    public function connect($url, $connectionTimeout = 5000)
    {
        $request = $this->client->getMessageFactory()->createRequest(
            $url,
            'GET',
            $connectionTimeout);
        $response = $this->client->getMessageFactory()->createResponse();
        $this->client->send($request, $response);
        if ($response->getStatus() != 200) {
            throw new ClientParsecException("URL $url not found", 404);
        }
        $this->crawler->addHtmlContent($response->getContent());
    }

    public function getDriver()
    {
        return $this->client;
    }

    /**
     * @param \DOMElement $element
     * @return bool|\DOMElement
     */
    private function getElementImage(\DOMElement $element)
    {
        $image = $element->getElementsByTagName('img');
        $result = false;
        if ($image->length) {
            $result = $image->item(0);
        }
        return $result;
    }

    public function getElements($selector)
    {
        $elements = $this->crawler->filter($selector);
        $result = new Matches();
        foreach ($elements as $element) {
            /** @var \DOMElement $element */
            $link = new LinkDOMComponent();
            $link->href = $element->getAttribute('href');
            $link->text = $element->textContent;
            $image = $this->getElementImage($element);
            if ($image) {
                $imageComponent = new ImageDomComponent();
                $imageComponent->src = $image->getAttribute('src');
                $imageComponent->alt = $image->getAttribute('alt');
                $link->addEmbed($imageComponent);
            }
            $result->addItem($link);
        }
        return $result;
    }
}