# README #

Script to create a database associated to a user+pwd. Used by KiwiCI.

Mandatory environmente variables to set:
- DB_HOST
- DB_ROOTUSER
- DB_ROOTPWD
- DB_USER
- DB_PWD
- DB_NAME
- MAX_QUERY_PER_HOUR (set to 0 to allow infinite)
- MAX_CONNECTIONS_PER_HOUR (set to 0 to allow infinite)
- MAX_UPDATES_PER_HOUR (set to 0 to allow infinite)
- MAX_USER_CONNECTIONS (set to 0 to allow infinite)

docker login
docker build . -t purplenetworksrl/db-creator
docker push purplenetworksrl/db-creator

