FROM redis:3.0.7

ADD redis.conf /etc/redis/redis.conf
ADD run.sh /usr/local/bin/run

RUN ["chmod", "+x", "/usr/local/bin/run"]

CMD ["/usr/local/bin/run"]