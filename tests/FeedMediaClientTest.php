<?php

/**
 * @file
 * Contains \MpxTest\FeedMediaClientTest.
 */

namespace MpxTest;

use GuzzleHttp\Client;
use Mpx\Services\FeedMedia\Client as FeedMediaClient;
use Mpx\Client as MpxClient;
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

    $container['base_url'] = 'http://feed.theplatform.com/f/';
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

}
