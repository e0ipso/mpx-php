# MPX PHP
Guzzle client that interacts with the MPX thePlatform video service.

## Usage example
```php
<?php

use Mpx\Services\FeedMedia\Client as FeedMediaClient;
use Pimple\Container;
use Mpx\Client as MpxClient;
use GuzzleHttp\Client;
use GuzzleHttp\Query;
use Mpx\Services\FeedMedia\Config;

require_once 'vendor/autoload.php';

// Prepare container.
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
    'account_pid' => '5NKIOC',
    'feed_pid' => 'F_jM8p%s39dL',
    'guids' => array('2822493')
  );
  return Config::createFromConfig($config);
};

// Create client.
$client = FeedMediaClient::create($container);

// Add additional query string parameters.
$query = new Query();
$query->add('form', MpxClient::FORMAT_CJSON);
$query->add('fields', 'id,guid');

var_dump($client->fetch(array('query' => $query)));
```
