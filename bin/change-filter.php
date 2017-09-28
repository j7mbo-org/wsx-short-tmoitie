<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Thruway\Transport\RawSocketClientTransportProvider;

if (!isset($argv[1])) {
    die('You must pass arguments to the script to use for the twitter filters, ie: php bin/change-filter.php hello morning night' . PHP_EOL);
}

unset($argv[0]);

$filters = array_values($argv);

$loop = Factory::create();

$client = new class('our-namespace', $loop, $filters) extends \Thruway\Peer\Client
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var string[]
     */
    private $filters;

    /**
     * @inheritdoc
     *
     * @param string $filterName
     */
    public function __construct(string $realm, LoopInterface $loop, array $filters) {

        $this->loop    = $loop;
        $this->filters = $filters;

        parent::__construct($realm, $loop);
    }

    /**
     * @inheritdoc
     */
    public function onSessionStart($session, $transport)
    {
        // @todo call() the RPC endpoint you create in the event handler
        //       remember, call takes args and argsKw parameters.
        //       Make sure you set aknowledge to true, and use then-> to close the session and stop the loop (see slides)
    }
};

/** How our client interacts with the broker (in this case, a raw socket!) **/
$client->addTransportProvider(new RawSocketClientTransportProvider('localhost', 1339));
$client->start();

