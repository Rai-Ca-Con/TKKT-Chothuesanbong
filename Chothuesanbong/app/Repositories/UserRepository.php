<?php
namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getAll()
    {
        return User::all();
    }

    public function findById($id)
    {
        return User::find($id) ?? null;
    }

    public function findByGoogleId($id)
    {
        return User::where('google_id', $id)->first();
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
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
