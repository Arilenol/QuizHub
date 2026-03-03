FROM php:latest
COPY . /usr/src/quizhub
RUN chmod +x /usr/src/quizhub/entrypoint.sh
WORKDIR /usr/src/quizhub
ENTRYPOINT ["./entrypoint.sh"]
EXPOSE 8080
CMD ["8080"]
