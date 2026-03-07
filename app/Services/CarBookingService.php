<?php

namespace App\Services;

use App\Models\CarBooking;
use Illuminate\Database\Eloquent\Collection;

class CarBookingService extends BaseService
{
    public function getAll(): Collection
    {
        return CarBooking::all();
    }

    public function findById(int $id): ServiceResponse
    {
        try {
            $booking = CarBooking::find($id);
            if (!$booking) {
                return ServiceResponse::error("Booking not found", 404);
            }
            return ServiceResponse::success($booking);
        } catch (\Throwable $e) {
            return ServiceResponse::error("Error finding booking: " . $e->getMessage(), 500, $e);
        }
    }

    public function create(array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($data) {
            return CarBooking::create($data);
        }, 'Create Car Booking');
    }

    public function update(CarBooking $booking, array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($booking, $data) {
            $booking->update($data);
            return $booking;
        }, 'Update Car Booking');
    }

    public function delete(CarBooking $booking): ServiceResponse
    {
        return $this->handleTransaction(function () use ($booking) {
            return $booking->delete();
        }, 'Delete Car Booking');
    }
}
