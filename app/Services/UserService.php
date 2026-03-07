<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    public function getAll(): Collection
    {
        return User::all();
    }

    public function findById(int $id): ServiceResponse
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return ServiceResponse::error("User not found", 404);
            }
            return ServiceResponse::success($user);
        } catch (\Throwable $e) {
            return ServiceResponse::error("Error finding user: " . $e->getMessage(), 500, $e);
        }
    }

    public function create(array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($data) {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            return User::create($data);
        }, 'Create User');
    }

    public function update(User $user, array $data): ServiceResponse
    {
        return $this->handleTransaction(function () use ($user, $data) {
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            $user->update($data);
            return $user;
        }, 'Update User');
    }

    public function delete(User $user): ServiceResponse
    {
        return $this->handleTransaction(function () use ($user) {
            return $user->delete();
        }, 'Delete User');
    }
}
