<?php

namespace App\Services;

use App\Models\TourCompany;
use Illuminate\Database\Eloquent\Collection;

class TourCompanyService extends BaseService
{
    public function getAll(): Collection
    {
        return TourCompany::all();
    }

    public function findById(int $id): ServiceResponse
    {
        try {
            $company = TourCompany::find($id);
            if (!$company) {
                return ServiceResponse::error("Tour company not found", 404);
            }
            return ServiceResponse::success($company);
        } catch (\Throwable $e) {
            return ServiceResponse::error("Error finding tour company: " . $e->getMessage(), 500, $e);
        }
    }

    public function create(array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($data) {
            return TourCompany::create($data);
        }, 'Create Tour Company');
    }

    public function update(TourCompany $company, array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($company, $data) {
            $company->update($data);
            return $company;
        }, 'Update Tour Company');
    }

    public function delete(TourCompany $company): ServiceResponse
    {
        return $this->handleTransaction(function () use ($company) {
            return $company->delete();
        }, 'Delete Tour Company');
    }
}
