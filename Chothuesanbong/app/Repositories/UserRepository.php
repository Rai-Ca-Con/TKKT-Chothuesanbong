<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getAllUser($perPage)
    {
        return User::where('is_admin', 0)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById($id)
    {
        return User::find($id) ?? null;
    }

    public function findByGoogleId($id)
    {
        return User::where('google_id', $id)->first();
    }

    public function getUserByKeyword($keyword, $perPage)
    {
        return User::where('is_admin', 0)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->findById($id);

        $user->update([
            'name' => $data['name'] ?? $user->name,
            'address' => $data['address'] ?? $user->address,
            'phone_number' => $data['phone_number'] ?? $user->phone_number,
            'avatar' => $data['avatar'] ?? $user->avatar,
            'refresh_token' => $data['refresh_token'] ?? $user->refresh_token
        ]);

        return $user->fresh();
    }

    public function delete($id)
    {
        $isDeleted = User::findOrFail($id)->delete();
        if ($isDeleted) {
            return $id;
        }
        return false;
    }
}
