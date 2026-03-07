<?php

namespace App\Services;

use App\Models\Car;
use Illuminate\Database\Eloquent\Collection;

class CarService extends BaseService
{
    public function getAll(): Collection
    {
        return Car::all();
    }

    public function findById(int $id): ServiceResponse
    {
        try {
            $car = Car::find($id);
            if (!$car) {
                return ServiceResponse::error("Car not found", 404);
            }
            return ServiceResponse::success($car);
        } catch (\Throwable $e) {
            return ServiceResponse::error("Error finding car: " . $e->getMessage(), 500, $e);
        }
    }

    public function create(array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($data) {
            return Car::create($data);
        }, 'Create Car');
    }

    public function update(Car $car, array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($car, $data) {
            $car->update($data);
            return $car;
        }, 'Update Car');
    }

    public function delete(Car $car): ServiceResponse
    {
        return $this->handleTransaction(function () use ($car) {
            return $car->delete();
        }, 'Delete Car');
    }
}
