<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;

class ReviewService extends BaseService
{
    public function getAll(): Collection
    {
        return Review::all();
    }

    public function findById(int $id): ServiceResponse
    {
        try {
            $review = Review::find($id);
            if (!$review) {
                return ServiceResponse::error("Review not found", 404);
            }
            return ServiceResponse::success($review);
        } catch (\Throwable $e) {
            return ServiceResponse::error("Error finding review: " . $e->getMessage(), 500, $e);
        }
    }

    public function create(array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($data) {
            return Review::create($data);
        }, 'Create Review');
    }

    public function update(Review $review, array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($review, $data) {
            $review->update($data);
            return $review;
        }, 'Update Review');
    }

    public function delete(Review $review): ServiceResponse
    {
        return $this->handleTransaction(function () use ($review) {
            return $review->delete();
        }, 'Delete Review');
    }
}
