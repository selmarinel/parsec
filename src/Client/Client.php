<?php

namespace Parsec\Client;

use JonnyW\PhantomJs\Client as PhantomClient;
use JonnyW\PhantomJs\Http\Request;
use JonnyW\PhantomJs\Http\Response;
use Parsec\Boot;
use Parsec\Exceptions\ClientParsecException;
use Parsec\Exceptions\PhantomEngineException;
use Parsec\Exceptions\PhantomParsecException;

class Client
{
    /** @var PhantomClient */
    private $client;
    /** @var Request */
    private $request;
    /** @var Response */
    private $response;

    /**
     * Client constructor.
     * @throws PhantomEngineException
     * @throws PhantomParsecException
     */
    public function __construct()
    {
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

    public function request($uri)
    {
        $this->request = $this->client->getMessageFactory()->createRequest($uri, 'GET');
    }

    public function response()
    {
        $this->response = $this->client->getMessageFactory()->createResponse();
    }

    /**
     * @param $uri
     * @throws ClientParsecException
     */
    public function connect($uri)
    {
        $this->request($uri);
        $this->response();
        $this->client->send($this->request, $this->response);
        if ($this->getResponse()->getStatus() != 200) {
            throw new ClientParsecException("URL $uri not found", 404);
        }
    }

    /**
     * @return PhantomClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}