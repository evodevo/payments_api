# Simple payments API

A simple payment processing REST API demo developed by applying Domain Driven Design principles 
with Unit tests using phpspec, functional tests using Behat and development environment based on docker containers.


To setup new docker dev environment:

```
make dev
```
NOTE: It requires docker and docker-compose version >= 2 to be installed. By default the API is accessible at 8080 port.

Generated API documentation is available at:

```
http://127.0.0.1:8080/api/doc
```


To run unit tests:

```
make unit
```


To run functional tests:

```
make test
```

To see the full command list:

```
make help
```