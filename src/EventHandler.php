<?php

namespace Workshop;

use Clue\React\Redis\Factory;
use Clue\React\Redis\Client as RedisClient;
use React\EventLoop\LoopInterface;
use Thruway\Peer\Client;

/**
 * @package Workshop
 */
class EventHandler extends Client
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var string
     */
    private $redisHostName = '';

    /**
     * @inheritdoc
     */
    public function __construct(string $realm, LoopInterface $loop)
    {
        $this->loop          = $loop;
        $this->redisHostName = gethostbyname('redis'); // needed for docker DNS lookup failures with React

        parent::__construct($realm, $loop);
    }

    /**
     * @inheritdoc
     */
    public function onSessionStart($session, $transport)
    {
        // @todo register an RPC endpoint to receive the filters, and put them in redis.
        //       the RPC endpoint should have a callback as a callable, as another function in this class
        //       Use \Redis, or even better, Clue\React\Redis\Client (RedisClient) as this is async (already installed)
        //       Redis hostname is in $this->>redisHostName
    }
}