<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class UserService
{
    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return User[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
         /** @var User|Builder */
        $query = User::query()->select(['users.*']);

        if ($name = Arr::get($filters, 'name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($email = Arr::get($filters, 'email')) {
            $query->where('email', $email);
        }

        if ($cpf = Arr::get($filters, 'cpf')) {
            $query->where('cpf', $cpf);
        }

        if ($phone = Arr::get($filters, 'phone')) {
            $query->where('phone', $phone);
        }

        if (in_array($orderBy, (new User)->getFillable())) {
            $query->orderBy("users.{$orderBy}", $sort);
        }

        return $query->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return User
     */
    public function store(array $requestData = []): User
    {
        $user = User::create(array_merge($requestData, [
            'password' => Str::random(20)
        ]));

        $user->roles()->sync(Arr::get($requestData, 'role_ids'));

        return $user;
    }

    /**
     * @param User $user
     * @param array $requestData
     * @return User
     */
    public function update(User $user, array $requestData = []): User
    {
        $user->update($requestData);

        $user->roles()->sync(Arr::get($requestData, 'role_ids'));

        return $user;
    }

    /**
     * @param User $user
     * @return boolean|null
     */
    public function delete(User $user): ?bool
    {
        $user->roles()->detach();

        return $user->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($user = User::find($id)) {
                $this->delete($user);
            }
        }
    }

    /**
     * @param User $user
     * @return User
     */
    public function activate(User $user): User
    {
        $user->update(['active' => true]);

        return $user;
    }

    /**
     * @param User $user
     * @return User
     */
    public function inactivate(User $user): User
    {
        $user->update(['active' => false]);

        return $user;
    }
}
