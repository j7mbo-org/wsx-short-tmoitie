var connection = new autobahn.Connection({url: 'ws://localhost:1338', realm: 'our-namespace'});

connection.onopen = function (session) {

    console.log('SESSION OPENED');

    /** @todo Subscribe to all twitter-based data - don't forget you'll get two params in your callback, args and argsKw **/
};

connection.open();