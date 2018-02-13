<?php
/**
 * Created by PhpStorm.
 * User: selma
 * Date: 13.02.2018
 * Time: 12:56
 */

namespace Parsec\Driver\MTS;


use DOMElement;
use MTS\Common\Devices\Browsers\Window;
use MTS\Factories;
use Parsec\Components\Matches;
use Parsec\DOMEntities\ImageDomComponent;
use Parsec\DOMEntities\LinkDOMComponent;
use Parsec\Driver\DriverInterface;
use Parsec\Driver\HasScenarios;
use Symfony\Component\DomCrawler\Crawler;

class Driver implements DriverInterface, HasScenarios
{
    /** @var Window */
    private $browserObj;

    /** @var Matches */
    private $elements;
    /** @var array */
    private $scenarios;

    public function __construct()
    {
        $this->elements = new Matches();
    }

    public function connect($url, $connectionTimeout = null)
    {
        $this->browserObj = Factories::getDevices()->getLocalHost()->getBrowser('phantomjs')->getNewWindow($url);
    }

    /**
     * @return Window
     */
    public function getDriver()
    {
        return $this->browserObj;
    }

    public function getElements($selector)
    {
        $content = $this->browserObj->getDom();

        $crawler = new Crawler();
        $crawler->addHtmlContent($content);
        $items = $crawler->filter($selector);

        foreach ($items as $element) {
            /** @var DOMElement $item */
            $link = new LinkDOMComponent();
            $link->href = $element->getAttribute('href');
            $link->text = $element->textContent;
            $images = $element->getElementsByTagName('img');
            if ($images->length) {
                $image = $images->item(0);
                $imageComponent = new ImageDomComponent();
                $imageComponent->src = $image->getAttribute('src');
                $imageComponent->alt = $image->getAttribute('alt');
                $link->addEmbed($imageComponent);
            }
            $this->elements->addItem($link);
        }
        return $this->elements;
    }

    public function close()
    {
        $this->browserObj->close();
    }

    public function setScenarios(Array $scenarios)
    {
        $this->scenarios = $scenarios;
    }

    public function getScenarios()
    {
        return $this->scenarios;
    }

    public function runScenarios()
    {
        foreach ($this->scenarios as $scenario) {
            if ($scenario instanceof ScenarioInterface) {
                $scenario->act($this->getDriver());
            }
        }
    }


}