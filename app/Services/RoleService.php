<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleService extends BaseService
{
    public function getAll(): Collection
    {
        return Role::all();
    }

    public function findById(int $id): ServiceResponse
    {
        try {
            $role = Role::find($id);
            if (!$role) {
                return ServiceResponse::error("Role not found", 404);
            }
            return ServiceResponse::success($role);
        } catch (\Throwable $e) {
            return ServiceResponse::error("Error finding role: " . $e->getMessage(), 500, $e);
        }
    }

    public function create(array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($data) {
            return Role::create($data);
        }, 'Create Role');
    }

    public function update(Role $role, array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($role, $data) {
            $role->update($data);
            return $role;
        }, 'Update Role');
    }

    public function delete(Role $role): ServiceResponse
    {
        return $this->handleTransaction(function () use ($role) {
            return $role->delete();
        }, 'Delete Role');
    }
}
