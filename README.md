# API-car-parking

The API allows users to perform CRUD (create, read, update, and delete) operations on a specific resource, such as a database of vehicles.

It also allows users to search for zones to park and retrieve real-time parking price based on which zones they parked.

Users can also start and stop parking using the API.

## Features

- Login/register user functionality
- Uses Laravel Sanctum's personal access token to authenticate
- User can perform CRUD operations for vehicles
- User can view zones and their parking rates
- User can start and stop parking
## Screenshots
![vehicle](https://user-images.githubusercontent.com/3273498/213993589-ae4453c8-f479-47cd-ae78-ba65950b06a2.png)
![parking](https://user-images.githubusercontent.com/3273498/213993865-89b93838-6ad1-49b7-838d-a4b00b5f903c.png)

## Installation

First, install backend dependencies

```bash
  composer install
```
Generate an .env file and edit it with your own database details

```bash
  cp .env.example .env
```
Then generate keys

```bash
  php artisan key:generate
```

Run migration code to setup database schema

```bash
  php artisan migrate
```

You may run the included feature tests if you wish

```bash
  php artisan test
```

## Documentation

To read about the API's documentation, you may visit it by typing `/docs` in the url
