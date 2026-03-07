<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionService extends BaseService
{
    public function getAll(): Collection
    {
        return Permission::all();
    }

    public function findById(int $id): ServiceResponse
    {
        try {
            $permission = Permission::find($id);
            if (!$permission) {
                return ServiceResponse::error("Permission not found", 404);
            }
            return ServiceResponse::success($permission);
        } catch (\Throwable $e) {
            return ServiceResponse::error("Error finding permission: " . $e->getMessage(), 500, $e);
        }
    }

    public function create(array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($data) {
            return Permission::create($data);
        }, 'Create Permission');
    }

    public function update(Permission $permission, array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($permission, $data) {
            $permission->update($data);
            return $permission;
        }, 'Update Permission');
    }

    public function delete(Permission $permission): ServiceResponse
    {
        return $this->handleTransaction(function () use ($permission) {
            return $permission->delete();
        }, 'Delete Permission');
    }
}
