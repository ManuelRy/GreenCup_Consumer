<?php

namespace App\Repository;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserRepository
{
    public function latest(): Collection
    {
        return User::orderBy('created_at', 'desc')->take(5)->get();
    }
    public function all(): Collection
    {
        return User::all();
    }

    public function create(array $data): Model
    {
        return User::create($data);
    }

    public function find(int $id): ?Model
    {
        return User::find($id);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->find($id);
        if ($user) {
            return $user->update($data);
        }
        return false;
    }

    public function delete(int $id): array
    {
        $user = $this->find($id);
        if ($user) {
            $user->delete();
            return ['message' => 'user deleted successfully'];
        }
        return ['message' => 'user not found'];
    }
}
