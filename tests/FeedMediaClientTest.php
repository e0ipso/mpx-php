<?php

/**
 * @file
 * Contains \MpxTest\FeedMediaClientTest.
 */

namespace MpxTest;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Query;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Mock;
use Mpx\MpxException;
use Mpx\Services\FeedMedia\Client as FeedMediaClient;
use Mpx\Client as MpxClient;
use Mpx\ClientInterface as MpxClientInterface;
use Mpx\Services\FeedMedia\Config as FeedMediaConfig;
use Pimple\Container;

class FeedMediaClientTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var FeedMediaClient
   */
  protected $client;

  /**
   * Set up the testing environment.
   */
  public function setUp() {
    parent::setUp();
    $container = new Container();

    $container['base_url'] = 'https://example.com';
    $container['guzzle_client'] = function ($c) {
      return new Client(array(
          'base_url' => $c['base_url'])
      );
    };
    $container['client'] = function ($c) {
      return new MpxClient($c['guzzle_client']);
    };

    $container['feed_config'] = function ($c) {
      $config = array(
        'client' => $c['client'],
        'account_pid' => 'HNK2IC',
        'feed_pid' => 'F_jM8Zls30dL',
        'guids' => array('2849493')
      );
      return FeedMediaConfig::createFromConfig($config);
    };

    $this->client = FeedMediaClient::create($container);
  }

  /**
   * Test build path.
   */
  public function testBuildPath() {
    $reflection_object = new \ReflectionObject($this->client);
    $reflection_method = $reflection_object->getMethod('buildPath');
    $reflection_method->setAccessible(TRUE);
    $this->assertEquals($reflection_method->invoke($this->client), 'HNK2IC/F_jM8Zls30dL/guid/-/2849493');
  }

  /**
   * Test fetch.
   */
  public function testFetch() {
    $xml_output = '<root><totalResults>90001</totalResults></root>';
    $json_output = '{"totalResults": "90001"}';
    $fake_responses[] = new Response(200, array('Content-Type' => 'application/json'), Stream::factory($xml_output));
    $fake_responses[] = new Response(200, array('Content-Type' => 'application/json'), Stream::factory($json_output));
    // Create a mock subscriber and the fake response.
    $mock = new Mock($fake_responses);
    $this->client->getGuzzleClient()->getEmitter()->attach($mock);

    // This also tests that xmlToArray works properly.
    $response = $this->client->fetch();
    $this->assertTrue(is_array($response));
    $this->assertEquals(90001, $response['totalResults']);

    // Test the JSON responses.
    $response = $this->client->fetch(array(
      'query' => new Query(array('form' => MpxClientInterface::FORMAT_JSON))
    ));
    $this->assertTrue(is_array($response));
    $this->assertEquals(90001, $response['totalResults']);
  }

  /**
   * Test fetch.
   */
  public function testCount() {
    $json_output = '{"totalResults": "90001"}';
    $fake_responses[] = new Response(200, array('Content-Type' => 'application/json'), Stream::factory($json_output));
    // Create a mock subscriber and the fake response.
    $mock = new Mock($fake_responses);
    $this->client->getGuzzleClient()->getEmitter()->attach($mock);

    try {
      $this->client->count(array(
        'query' => new Query(array('form' => MpxClientInterface::FORMAT_JSON))
      ));
      $this->fail('Count with GUID should throw an exception.');
    }
    catch (MpxException $e) {
      $this->assertTrue(TRUE, 'Count with GUID throws an exception.');
    }

    $this->client->setConfig('guids', array());
    $response = $this->client->fetch(array(
      'query' => new Query(array('form' => MpxClientInterface::FORMAT_JSON))
    ));
    $this->assertEquals(90001, $response['totalResults']);
  }

}
