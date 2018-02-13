<?php

namespace Parsec;

use Parsec\Components\Matches;
use Parsec\Components\Site;
use Parsec\DOMEntities\ImageDomComponent;
use Parsec\DOMEntities\LinkDOMComponent;
use Parsec\Driver\DriverInterface;
use Parsec\Driver\HasScenarios;
use Parsec\Driver\ScenarioInterface;
use Parsec\Driver\Selenium\Scenarios\AbstractStreamingScenario;
use Parsec\Exceptions\ParsecException;

class Handler
{
    /** @var DriverInterface */
    private $driver;

    /**
     * Handler constructor.
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param $uri
     * @param int $timeout
     */
    public function load($uri, $timeout = 5000)
    {
        $this->driver->connect($uri, $timeout);
    }

    /**
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    public function setScenarios(array $scenarios)
    {
        if ($this->driver instanceof HasScenarios) {
            $this->driver->setScenarios($scenarios);
        }
    }

    /**
     * @param array $sites
     * @return Matches
     * @throws ParsecException
     */
    public function report(Array $sites)
    {
        $result = new Matches();;
        foreach ($sites as $site) {
            $siteComponent = new Site();
            $siteComponent->linkFrom = $site['host'];
            $siteComponent->linkTo = $site['href'];
            $siteComponent->anchor = $site['anchor'];
            $siteComponent->status = Site::NOT_FOUND;

            try {
                $this->load($site['host']);

                if ($this->driver instanceof HasScenarios) {
                    foreach ($this->hackForUniqueScenarios($this->driver->getScenarios()) as $scenario) {

                        if ($scenario instanceof ScenarioInterface) {
                            $scenario->act($this->driver->getDriver());
                        }
                        $this->findLinks($siteComponent, $site);
                    }
                } else {
                    $this->findLinks($siteComponent, $site);
                }

            } catch (\Exception $exception) {
                $siteComponent->status = Site::EXCEPTION;
                throw new ParsecException($exception->getMessage(), $exception->getCode(), $exception);
            } finally {
                $result->addItem($siteComponent);
                $this->driver->close();
            }
        }
        return $result;
    }

    public function findLinks(Site $siteComponent, array $site)
    {
        /** @var Matches $links */
        $links = $this->driver->getElements("a[href='{$site['href']}']");
        $siteComponent->links = array_merge($siteComponent->links, $links->all());
        if ($links->count()) {
            $siteComponent->status = $this->checkLinks(
                $links,
                $site['anchor']) ? Site::LIVE : Site::ANCHOR_MISMATCH;
        }
    }

    /**
     * TODO BAD CODE!!!!!
     * @param array $scenarios
     * @return array
     */
    private function hackForUniqueScenarios(array $scenarios)
    {
        $result = [];
        foreach ($scenarios as $index => $scenario) {

            if ($scenario instanceof AbstractStreamingScenario) {
                $newScenario = new AbstractStreamingScenario($scenario->getDriver());
                $newScenario->setCssElement($scenario->getCssElement());

                unset($scenarios[$index]);
                $result[$index] = $newScenario;

            } else {
                $result[$index] = $scenario;
            }
        }
        return $result;
    }

    /**
     * @param Matches $links
     * @param string $checkAnchor
     * @return bool
     */
    public function checkLinks(Matches $links, $checkAnchor = '')
    {
        $result = false;
        foreach ($links->all() as $link) {
            /** @var  LinkDOMComponent $link */
            if (mb_strripos($link->text, $checkAnchor) !== false) {
                $result = true;
            }
            if (!empty($link->embedded)) {
                foreach ($link->embedded as $item) {
                    if ($item instanceof ImageDomComponent) {
                        if (mb_strripos($item->src, $checkAnchor) !== false) {
                            $result = true;
                        }
                    }
                }
            }
        }
        return $result;
    }
}