# WSX

### Launching

Create a docker network so all the containers can communicate:

`docker network create dev_network`

Launch the docker container with:

`docker-compose up -d --build`

Access the container with:

`docker-compose exec php /bin/bash`

After running the docker container, visit this to see your tasks:

`http://localhost:1337`

### Troubleshooting

> localhost:1337 doesn't show anything

This has been seen with users using older versions of docker started in terminal only (and not the GUI docker tool).


There's an issue with using docker and forwarding ports to the host machine. Make sure that you have no other docker 
containers running and that you have stopped any other virtual machines, Apache and Skype. Restart your computer. Also
make sure you have the latest version of docker. If this has not fixed anything, you can pair program with another
workshop member.

> I keep getting 402 errors in twitter-stream.php.

Twitter rate limits you if you keep stopping and starting your script, as you have to authenticated each time.

Create a new application and set of credentials on apps.twitter.com and use these instead.

### Stopping

To stop / remove the container, run:

`docker-compose stop` and/or `docker-compose rm`

To delete all docker containers from the system and start again from scratch, run:

`docker system prune -a`