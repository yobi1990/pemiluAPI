<?php

use Silex\WebTestCase;

class EndpointsTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../src/app.php';
        $app['debug'] = true;
        $app['exception_handler']->disable();

        return $app;
    }

    public function testListAvailableEndpoints()
    {
        $client = $this->createClient();
        $client->followRedirects();
        $client->request('GET', 'endpoints', array('apiKey' => '06ec082d057daa3d310b27483cc3962e'));
        $response = $client->getResponse();
        $content = json_decode($response->getContent());

        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testRetrieveAnEndpointDetails()
    {
        $client = $this->createClient();
        $client->followRedirects();
        $client->request('GET', 'endpoints/pemilu-news', array('apiKey' => '06ec082d057daa3d310b27483cc3962e'));
        $response = $client->getResponse();
        $content = json_decode($response->getContent());

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($content->data->name, 'Pemilu News');
    }

    public function testInvalidApiKey()
    {
        $client = $this->createClient();
        $client->followRedirects();

        // GET /endpoints/{slug}/?{apiKey}
        $client->request('GET', 'endpoints/pemilu-news', array('apiKey' => 'insertinvalidkeyhere'));
        $response = $client->getResponse();
        $content = json_decode($response->getContent());

        $this->assertEquals($response->getStatusCode(), 401);
        $this->assertEquals($content->error->type, 'invalid_request_error');

        // GET /endpoints?{apiKey}
        $client->request('GET', 'endpoints', array('apiKey' => 'insertinvalidkeyhere'));
        $response = $client->getResponse();
        $content = json_decode($response->getContent());

        $this->assertEquals($response->getStatusCode(), 401);
        $this->assertEquals($content->error->type, 'invalid_request_error');
    }

    public function testInvalidSlug()
    {
        $client = $this->createClient();
        $client->followRedirects();
        $client->request('GET', 'endpoints/foo-bar', array('apiKey' => '06ec082d057daa3d310b27483cc3962e'));
        $response = $client->getResponse();
        $content = json_decode($response->getContent());

        $this->assertEquals($response->getStatusCode(), 404);
        $this->assertEquals($content->error->type, 'data_not_found');
    }
}