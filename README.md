## Running the project

Clone the repo 
```
    git clone https://github.com/Domahawk/mini-webshop.git
```

### Steps:
Enter root of the project:
```
cd mini-webshop
```
Get `.env` file
```
cp .env.example .env
```
Build containers:
```
docker compose build
```
Start containers:
```
docker compose up -d
```
Install dependencies:
```
docker compose run php composer install
```
Run migrations and seeders:
```~~~~
docker compose run php php artisan migrate --seed
```
If `SQLSTATE[HY000] [2002] Connection refused` error appears, wait a few minutes for DB to finish setting up and run migration again. 
