# WSX

### Launching

Create a docker network so all the containers can communicate:

`docker network create dev_network`

Launch the docker container with:

`docker-compose up -d --build`

Access the container with:

`docker-compose php exec /bin/bash`

After running the docker container, visit this to see your tasks:

`http://localhost:1337`

### Troubleshooting

> I keep getting 402 errors in twitter-stream.php.

Twitter rate limits you if you keep stopping and starting your script, as you have to authenticated each time.

Create a new application and set of credentials on apps.twitter.com and use these instead.

### Stopping

To stop / remove the container, run:

`docker-compose stop` and/or `docker-compose rm`

To delete all docker containers from the system and start again from scratch, run:

`docker system prune -a`