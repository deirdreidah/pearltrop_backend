<?php

namespace App\Services;

use App\Models\Accommodation;
use Illuminate\Database\Eloquent\Collection;

class AccommodationService extends BaseService
{
    public function getAll(): Collection
    {
        return Accommodation::all();
    }

    public function findById(int $id): ServiceResponse
    {
        try {
            $accommodation = Accommodation::find($id);
            if (!$accommodation) {
                return ServiceResponse::error("Accommodation not found", 404);
            }
            return ServiceResponse::success($accommodation);
        } catch (\Throwable $e) {
            return ServiceResponse::error("Error finding accommodation: " . $e->getMessage(), 500, $e);
        }
    }

    public function create(array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($data) {
            return Accommodation::create($data);
        }, 'Create Accommodation');
    }

    public function update(Accommodation $accommodation, array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($accommodation, $data) {
            $accommodation->update($data);
            return $accommodation;
        }, 'Update Accommodation');
    }

    public function delete(Accommodation $accommodation): ServiceResponse
    {
        return $this->handleTransaction(function () use ($accommodation) {
            return $accommodation->delete();
        }, 'Delete Accommodation');
    }
}
