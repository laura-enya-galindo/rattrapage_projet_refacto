<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    
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

        $content = $client->getResponse()->getContent();
        $this->assertJsonStringEqualsJsonString('[{"id":1,"name":"John","age":25},{"id":2,"name":"Jane","age":22},{"id":3,"name":"Jack","age":27}]', $content);
    }

}
