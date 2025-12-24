<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getByRole($role)
    {
        return $this->model->where('role', $role)->get();
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function search($keyword)
    {
        return $this->model->where(function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%');
        })->get();
    }

    public function searchAndFilter($keyword = null, $role = null)
    {
        $query = $this->model->newQuery();

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                  ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getAdmins()
    {
        return $this->getByRole(User::ROLE_ADMIN);
    }

    public function getManajers()
    {
        return $this->getByRole(User::ROLE_MANAJER);
    }

    public function getStaffs()
    {
        return $this->getByRole(User::ROLE_STAFF);
    }
}
