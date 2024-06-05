# Messenger Issue

## Installation

1. Run docker containers

```shell
docker-compose up -d
```

2. Install dependencies
```shell
docker-compose exec -it application composer install
```

## Run application

1. Start consumer
```shell
docker-compose exec -it application php ./bin/console messenger:consume event_websocket -vvv
```

2. Dispatch message
```shell
docker-compose exec -it application php ./bin/console app:dispatch App\\Message\\WebsocketMessage
```