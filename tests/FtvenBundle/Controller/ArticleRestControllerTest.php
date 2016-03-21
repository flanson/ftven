<?php

namespace Tests\FtvenBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\FtvenBundle\Fixtures\LoadArticleData;

class ArticlesRestControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient();
    }


    public function testJsonGetArticleListAction()
    {
        $fixtures = array('Tests\FtvenBundle\Fixtures\LoadArticleData');
        $this->loadFixtures($fixtures);
        $stories = LoadArticleData::$stories;

        $this->client->request('GET', '/articles/');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertCount(count($stories), $decoded);
    }

    protected function assertJsonResponse(
        $response,
        $statusCode = 200,
        $checkValidJson =  true,
        $contentType = 'application/json'
    )
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', $contentType),
            $response->headers
        );

        if ($checkValidJson) {
            $decode = json_decode($response->getContent());
            $this->assertTrue(($decode != null && $decode != false),
                'is response valid json: [' . $response->getContent() . ']'
            );
        }
    }
}
