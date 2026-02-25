FROM php:latest
COPY . /usr/src/quizhub
COPY config/php.ini /usr/local/etc/php/php.ini
RUN chmod +x /usr/src/quizhub/entrypoint.sh
WORKDIR /usr/src/quizhub
VOLUME ["/usr/src/quizhub/database"]
ENTRYPOINT ["./entrypoint.sh"]
EXPOSE 8080
CMD ["8080"]
