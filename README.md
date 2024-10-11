# Localstack UI

Localstack UI is a web application designed to provide a user-friendly interface 
for managing and interacting with Localstack services. 
This project integrates various components such as a frontend, API, Laravel Reverb,
Redis, MySQL, and Horizon.

## Prerequisites

- Docker
- Docker Compose
- PHP >= 8.2
- SQLite
- PHP SQLite extension

## Installation

1. **Clone the repository:**

    ```sh
    git clone https://github.com/yourusername/localstack-ui.git
    cd localstack-ui
    ```

2. **Build and run the Docker containers:**

    ```sh
    docker-compose up --build
    ```

## Configuration

Ensure the following environment variables are set in your `.env` file:

```dotenv
DB_CONNECTION=mysql
DB_HOST=0.0.0.0
DB_PORT=3306
DB_DATABASE=localstackui
DB_USERNAME=localstackui-api
DB_PASSWORD=localstack@123

REDIS_HOST=0.0.0.0
REDIS_PORT=6379

AWS_ENDPOINT=http://0.0.0.0:4566
