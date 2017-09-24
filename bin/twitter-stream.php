<?php

require_once __DIR__ . '/../vendor/autoload.php';

// @todo Sign up on apps.twitter.com and get your four tokens required for an application
// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "");
define("TWITTER_CONSUMER_SECRET", "");
// The OAuth data for the twitter account
define("OAUTH_TOKEN", "");
define("OAUTH_SECRET", "");

class TweetFilterConsumer extends OauthPhirehose
{
    /**
     * @var \AMQPExchange
     */
    private $exchange;

    /**
     * @var Redis
     */
    private $redis;

    /**
     * @inheritdoc
     */
    public function __construct(\AMQPExchange $exchange)
    {
        $this->exchange = $exchange;

        $this->redis = new \Redis;

        if (!$this->redis->connect('redis'))
        {
            throw new \RuntimeException("Could not connect to Redis");
        }

        parent::__construct(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
    }

    /**
     * @inheritdoc
     *
     * We are supposed to override this method. It is called every time a tweet is streamed (so A LOT)
     */
    public function enqueueStatus($status)
    {
        $data = @json_decode($status, true);

        if (!is_array($data) || !isset($data['user']['screen_name']) || json_last_error() !== JSON_ERROR_NONE)
        {
            return;
        }

        $store = $data['user']['screen_name'] . ': ' . urldecode($data['text']);

        /** Publish as a simple string **/
        // @todo Publish the $store variable to RabbitMQ
    }

    /**
     * @inheritdoc
     *
     * Phirehose calls this method every ~5 seconds, and we can use it to check for filter changes in redis
     */
    public function checkFilterPredicates()
    {
        $filter = $this->redis->get('filter');

        if (!$filter || $filter === '') {
            return;
        }

        $filters = json_decode($filter);

        if ($this->getTrack() !== $filters)
        {
            $this->setTrack(json_decode($filter));

            printf(
                '[%s] - New filters received: "%s" %s',
                (new \DateTime)->format('H:i:s'),
                $filter,
                PHP_EOL
            );
        }
    }
}

/**
 * Connecting to RabbitMQ correctly has been done for you. You publish() on an exchange
 *
 * @return \AMQPExchange
 */
function createExchange(): \AMQPExchange
{
    $connection = new \AMQPConnection(
        [
            'host'     => 'rabbitmq',
            'port'     => 5672,
            'login'    => 'rabbitmq',
            'password' => 'rabbitmq',
            'vhost'    => '/'
        ]
    );

    $connection->connect();

    $channel = new \AMQPChannel($connection);

    $exchange = new \AMQPExchange($channel);
    $exchange->setName('twitter_exchange');
    $exchange->setType(AMQP_EX_TYPE_DIRECT);
    $exchange->declareExchange();

    $queue = new \AMQPQueue($channel);
    $queue->setName('twitter_queue');
    $queue->setArgument('x-max-length', 1000); // messages will be dropped or dead-lettered to make space for new ones
    $queue->setFlags(AMQP_AUTOACK);
    $queue->declareQueue();

    $queue->bind('twitter_exchange');

    return $exchange;
}

/** Start streaming with Phirehose **/
$sc = new TweetFilterConsumer(createExchange());
$sc->setTrack(['morning', 'goodnight']);
$sc->consume();