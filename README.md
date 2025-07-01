# S9ili - Smart Irrigation System API

## Overview
S9ili is a RESTful API for smart irrigation management, providing real-time field monitoring and automated watering control.

## Hosting
The API is hosted at: `http://172.201.116.22`

## Features
- Real-time weather data integration
- Automated crop growth stage tracking
- Smart irrigation scheduling
- Multiple watering methods support
- Multiple fields methods support

## Tech Stack
- PHP 8.1+
- Laravel 10
- SQLite Database
- MeteoBLUE Weather API

## Installation

```bash
# Clone the repository
git clone https://github.com/TafatAbderrahim/NCSHACK-agrico.git

# Navigate to project directory
cd NCSHACK-agrico

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start server
php artisan serve --host=172.201.116.22 --port=80
```

## API Base URL
```
http://172.201.116.22/api
```

## API Documentation

### Field Management

```http
GET /api/fields
POST /api/fields
GET /api/fields/{id}
PUT /api/fields/{id}
DELETE /api/fields/{id}
```

### Watering Logs

```http
GET /api/watering-logs
POST /api/watering-logs
GET /api/watering-logs/{id}
PUT /api/watering-logs/{id}
DELETE /api/watering-logs/{id}
```

### Crop Management

```http
GET /api/crops
POST /api/crops
GET /api/crops/{id}
PUT /api/crops/{id}
DELETE /api/crops/{id}
```

## Data Models

### Field
```json
{
    "id": 1,
    "name": "Field A",
    "surface": 100.5,
    "crop_id": 1,
    "temperature": 24.63,
    "moisture": 87,
    "condition": "good",
    "valve_state": false
}
```

### WateringLog
```json
{
    "id": 1,
    "field_id": 1,
    "start_time": "2025-06-30T09:00:00Z",
    "end_time": "2025-06-30T09:30:00Z",
    "method": "SPRINKLER"
}
```

### Crop
```json
{
    "id": 1,
    "name": "Tomatoes",
    "watering_frequency": 0.75,
    "growth_stage": "SEED"
}
```

## License
MIT License

## Authors
- Tafat Abderrahim
