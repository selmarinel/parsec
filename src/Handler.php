<?php

namespace Parsec;

use Parsec\Components\Matches;
use Parsec\Components\Site;
use Parsec\DOMEntities\ImageDomComponent;
use Parsec\DOMEntities\LinkDOMComponent;
use Parsec\Driver\DriverInterface;
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
                $this->driver->connect($site['host']);
                /** @var Matches $links */
                $links = $this->driver->getElements("a[href='{$site['href']}']");
                $siteComponent->links = $links->all();
                if ($links->count()) {
                    $siteComponent->status = $this->checkLinks(
                        $links,
                        $site['anchor']) ? Site::LIVE : Site::ANCHOR_MISMATCH;
                }
            } catch (\Exception $exception) {
                throw new ParsecException($exception->getMessage(),$exception->getCode(),$exception);
            } finally {
                $result->addItem($siteComponent);
                $this->driver->close();
            }
        }
        return $result;
    }

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