# Centra

This is PHP project used to connect to github-api to display the issues that are 
* Queued
* Active
* Completed

## Prerequisites

1. Need docker to run this project
   * Please refer this [official documentation](https://docs.docker.com/engine/install/) to install docker, docker engine and docker composer.
   
## Steps For Installation 

* Got to the root directory and execute ```./run.sh```
  * This will install PHP7.3-fpm
  * Xdebug 2.5.5
  * Composer (LTS)

## Steps For Configuration

* Rename .env.sample file with .env
* Replace values for below mentioned Keys.
  * SOURCE_DIR=Path to root directory of the project 
  * GH_ACCOUNT=Your github user name e.g mine is  ``Mubasher693``
  * GH_REPOSITORIES=All the repositories for which you want to populate board for but seprate each with `|` sign. For example
      ``symfony-fos-restfull-with-jwt-token|Mubasher693``
  * GH_CLIENT_ID=Your github client id 
  * GH_CLIENT_SECRET=Your github client secret
  

## Run The Project

* Open any browser and hit ``http://localhost:8080/``