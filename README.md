# WP Mango 

A companion plugin for [Mango](https://github.com/axelspringer/mango), which extends the WordPress REST API.

## Getting Started

You can use the plugin and the bootstrap classes via

```
composer require axelspringer/wp-mango dev-master
```

When you enable the plugin it generates an access token and an access secret key. These are shown in `Settings > WP Mango`. They change in any case you change the settings of the plugin.

## Development

We use [Docker Compose](https://docs.docker.com/compose/) to provide a local development environment across projects. The WordPress listen at [localhost:8181](http://localhost:8181/wp-admin). The progress is saved in docker volumes.

```
# Run
docker-compose up
```

# License
[Apache-2.0](/LICENSE)
