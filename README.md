# Currency Conversion API
This is a simple API to perform currency conversions using the Fixer.io API as a currency exchange rate provider. The API provides a POST endpoint that accepts source and target currencies along with the amount to convert, stores each request in the database, and returns the converted value.

## Requirements
- PHP 8.2+
- Composer (for dependency management)
- Docker and Docker Compose (to simplify setup)

## Setup Instructions
### 1. Clone the Repository
```
git clone https://github.com/your-username/your-repository-name.git
cd your-repository-name 
```
### 2. Set Up the Environment Variables
1. Duplicate the ```.env.example``` file to create a new .env file:
   ```
   cp .env.example .env
   ```
2. Open ```.env``` and update the following fields:
```
FIXER_API_KEY=your_fixer_api_key
```
### 3. Build and Start the Docker Containers
This setup uses Docker Compose to build and start the necessary containers, which include the Laravel application, the database, and possibly other services.

To build and run the containers:

```
docker-compose up -d --build
```
This command will:

- Build and start the application container.
- Start a MySQL database container with default settings (adjustable in the ```docker-compose.yml``` and ```.env``` files).
### 4. Install Dependencies
Once the containers are up, install the project dependencies via Composer:
```
docker-compose exec app composer install
```

### 5. Run Database Migrations
Set up the database schema by running the migrations:
```
docker-compose exec app php artisan migrate
```
### 6. Running Tests
This project includes a test to verify that the API endpoint is working correctly.

Run the tests with the following command:
```
docker-compose exec app php artisan test
```

## Usage
### API Endpoint
#### POST /api/convert

To perform a currency conversion, send a POST request with the following JSON payload:

- source_currency: 3-letter ISO currency code for the source currency (e.g., "USD").
- target_currency: 3-letter ISO currency code for the target currency (e.g., "EUR").
- amount: The amount in the source currency to convert.
  Example request using curl:
```
curl -X POST http://localhost:8000/api/convert \
-H "Content-Type: application/json" \
-d '{"source_currency": "USD", "target_currency": "EUR", "amount": 100}'
```

