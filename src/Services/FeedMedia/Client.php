<?php
/**
 * @file
 * Contains \Mpx\Services\FeedMedia\Client
 */

namespace Mpx\Services\FeedMedia;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Query;
use Mpx\MpxException;
use Mpx\ClientInterface as MpxClientInterface;
use Pimple\Container;

/**
 * Class FeedMediaMedia
 *
 * @package Mpx\Services
 *
 * @see http://help.theplatform.com/display/vms2/Requesting+Media+feeds
 */
class Client implements ClientInterface {

  /**
   * The MPX client.
   *
   * @var MpxClientInterface
   */
  protected $client;

  /**
   * The Account.pid of the account that owns the feed.
   *
   * @see http://help.theplatform.com/display/wsf2/Account.pid
   *
   * @var string
   */
  protected $accountPid;

  /**
   * The FeedConfig.pid of the feed.
   *
   * @see http://help.theplatform.com/display/fs3/FeedConfig.pid
   *
   * @var string
   */
  protected $feedPid;

  /**
   * Specifies a subfeed of the main feed. This corresponds to a
   * SubFeed.FeedType value of an item in the FeedConfig.subFeeds field.
   *
   * @see http://help.theplatform.com/display/fs3/SubFeed+object
   * @see http://help.theplatform.com/display/fs3/FeedConfig.subFeeds
   *
   * @var string
   */
  protected $feedType;

  /**
   * The feed path segment forces the response to be in feed format, regardless
   * of the number of items in the feed. A feed that returns multiple items is
   * always in feed format. However, a feed with only 1 item returns the item as
   * an object by default. If the feed path segment is included, a feed with
   * only 1 item returns a feed with only 1 item.
   *
   * @var bool
   */
  protected $feed;

  /**
   * A list of numeric IDs for individual items in the feed. This path segment
   * cannot be used with the guid/<owner ID>/<GUIDs> path segment.
   *
   * @var array
   */
  protected $ids = array();

  /**
   * Contains either an owner ID or a dash (—). GUIDs are only guaranteed to be
   * unique within an account. Because some feeds can be configured to include
   * inherited objects from other accounts, it is possible that 2 objects in a
   * feed could have the same GUID. You can include the object's ownerId to
   * uniquely identify the object. Alternatively, you can include a dash (—) to
   * specify that the owner ID is the owner ID of the FeedConfig.
   *
   * @see http://help.theplatform.com/display/fs3/FeedConfig+endpoint
   *
   * @var string
   */
  protected $ownerId;

  /**
   * Globally unique identifiers.
   *
   * @var array
   */
  protected $guids = array();

  /**
   * A list of SEO terms for the feed.
   *
   * @var array
   */
  protected $seoTerms = array();

  /**
   * Query params.
   *
   * @var Query
   */
  protected $queryParams;

  /**
   * Constructs a Client object.
   *
   * @param MpxClientInterface $client
   * @param $accountPid
   * @param $feedPid
   * @param $feedType
   * @param $feed
   * @param $ids
   * @param $ownerId
   * @param $guids
   * @param $seoTerms
   */
  public function __construct(MpxClientInterface $client, $accountPid, $feedPid, $feedType = NULL, $feed = FALSE, $ids = array(), $ownerId = NULL, $guids = array(), $seoTerms = array(), Query $query_params = NULL) {
    $this->client = $client;
    $this->accountPid = $accountPid;
    $this->feedPid = $feedPid;
    $this->feedType = $feedType;
    $this->feed = $feed;
    $this->ids = $ids;
    $this->ownerId = $ownerId;
    $this->guids = $guids;
    $this->seoTerms = $seoTerms;
    $this->queryParams = $query_params ? $query_params : new Query();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(Container $container) {
    /** @var Config $feed_config */
    $feed_config = $container['feed_config'];
    $feed_type = $feed_config['feed_type'] ? $feed_config['feed_type'] : NULL;
    $feed = $feed_config['feed'] ? $feed_config['feed'] : FALSE;
    $ids = $feed_config['ids'] ? $feed_config['ids'] : array();
    $owner_id = $feed_config['owner_id'] ? $feed_config['owner_id'] : NULL;
    $guids = $feed_config['guids'] ? $feed_config['guids'] : NULL;
    $seo_terms = $feed_config['seo_terms'] ? $feed_config['seo_terms'] : NULL;
    $query_params = $feed_config['query_params'] ? $feed_config['query_params'] : new Query();
    return new static($feed_config['client'], $feed_config['account_pid'], $feed_config['feed_pid'], $feed_type, $feed, $ids, $owner_id, $guids, $seo_terms, $query_params);
  }

  /**
   * {@inheritdoc}
   */
  public function fetch(array $options = array()) {
    $options += array('query' => $this->queryParams);
    try {
      $response = $this->client->get($this->buildPath(), $options);
    }
    catch (RequestException $e) {
      throw new MpxException(sprintf("Request exception: %s\n%s", $e->getMessage(), print_r($e->getRequest(), TRUE)));
    }
    if (!$response instanceof Response) {
      return NULL;
    }
    return $this->client->parseBody($response, $options['query']->get('form'));
  }

  /**
   * {@inheritdoc}
   */
  public function count(array $options = array()) {
    if ($this->ids || $this->guids) {
      throw new MpxException('You cannot add IDs or GUIDs to count query.');
    }
    $options += array('query' => $this->queryParams);
    $options['query']->add('entries', FALSE);
    $options['query']->add('count', TRUE);

    $result = $this->fetch($options);
    return $result['totalResults'];
  }

  /**
   * Builds the path to request based on the internal parameters.
   *
   * @throws MpxException
   *   For incompatible options.
   *
   * @return string
   *   The path.
   */
  protected function buildPath() {
    if (!empty($this->ids) && $this->guids) {
      // If there is information about both the IDs and GUIDs, then throw an
      // exception.
      throw new MpxException(sprintf('Cannot provide IDs and GUIDs for the %s client.', __CLASS__));
    }
    $path_parts[] = $this->accountPid;
    $path_parts[] = $this->feedPid;
    $path_parts[] = $this->feedType ? $this->feedType : NULL;
    $path_parts[] = $this->feed ? 'feed' : NULL;
    $path_parts[] = $this->ids ? implode(',', $this->ids) : NULL;
    if ($this->guids) {
      $path_parts[] = 'guid';
      $path_parts[] = $this->ownerId ? $this->ownerId : '-';
      $path_parts[] = implode(',', $this->guids);
    }
    $path_parts[] = $this->seoTerms ? implode(',', $this->seoTerms) : NULL;

    // Remove all the empty parts.
    $path_parts = array_filter($path_parts);

    return implode('/', $path_parts);
  }

}
