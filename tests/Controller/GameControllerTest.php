<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;

class GameControllerTest extends WebTestCase
{
    use RecreateDatabaseTrait;

    /**
     * @dataProvider dataprovider_getPartieList_checkAuthorizedMethods
     */
    public function test_getPartieList_checkAuthorizedMethods($method){
        $client = static::createClient();
        $client->request($method, '/games');
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }

    private static function dataprovider_getPartieList_checkAuthorizedMethods(): array
    {
        return [
            ['PUT'],
            ['DELETE'],
            ['PATCH'],
        ];
    }

     public function test_getPartieList_checkReturnStatus(){
        $client = static::createClient();
        $client->request('GET', '/games');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_getPartieList_checkValues(){
        $client = static::createClient();
        $client->request('GET', '/games');

        $content = json_decode($client->getResponse()->getContent());

        $expectedResult = json_decode(file_get_contents(__DIR__.'/expect/test_getPartieList_checkValues.json'));
        $this->assertEquals($expectedResult, $content);
    }

    /**
     * @dataProvider dataprovider_getGameInfo_checkAuthorizedMethods
     */
    public function test_getGameInfo_checkAuthorizedMethods(string $method){
        $client = static::createClient();
        $client->request($method, '/game/1');
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }

    private static function dataprovider_getGameInfo_checkAuthorizedMethods(): array
    {
        return [
            ['POST'],
            ['PUT'],
        ];
    }

    /**
     * @dataProvider dataprovider_getGameInfo_checkWithInvalidId
     */
    public function test_getGameInfo_checkWithInvalidId($id){
        $client = static::createClient();
        $client->request('GET', '/game/'.$id);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    private static function dataprovider_getGameInfo_checkWithInvalidId(): array
    {
        return [
            [0],
            [-1],
            ['a'],
        ];
    }

    public function test_getGameInfo_checkReturnStatus(){
        $client = static::createClient();
        $client->request('GET', '/game/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function test_getGameInfo_checkValues(){
        $client = static::createClient();
        $client->request('GET', '/game/4');

        $content = $client->getResponse()->getContent();
        $this->assertJsonStringEqualsJsonString('{"id":4,"state":"finished","playLeft":"paper","playRight":"scissors","result":"winLeft","playerLeft":{"id":1,"name":"John","age":25},"playerRight":{"id":2,"name":"Jane","age":22}}', $content);
    }
}
