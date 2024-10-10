# Localstack UI

Localstack UI is a web application designed to provide a user-friendly interface for managing and interacting with Localstack services. This project integrates various components such as a frontend, API, Laravel Reverb, Redis, MySQL, and Horizon.

## Prerequisites

- Docker
- Docker Compose

## Installation

1. **Clone the repository:**

    ```sh
    git clone https://github.com/yourusername/localstack-ui.git
    cd localstack-ui
    ```

2. **Set up environment variables:**

    Copy the `.env.example` to `.env` and adjust the configurations as needed.

    ```sh
    cp .env.bkp.example .env.bkp
    ```

3. **Build and run the Docker containers:**

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
```

## Usage

Once the containers are up and running, you can access the application at `http://localhost`. The Localstack services will be available at `http://localhost:4566`.

## Development

### Frontend

Navigate to the `frontend` directory and install the dependencies:

```sh
cd frontend
npm install
npm run dev
```

### Backend

Install the PHP dependencies using Composer:

```sh
composer install
```

Run the Laravel development server:

```sh
php artisan serve
```

## Deployment

This project uses GitLab CI for continuous integration and deployment. The `.gitlab-ci.yml` file is configured to build and push the Docker image to Docker Hub.

## Contributing

Feel free to submit issues or pull requests. For major changes, please open an issue first to discuss what you would like to change.

## License

This project is licensed under the MIT License. See the `LICENSE` file for more details.
