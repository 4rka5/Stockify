<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->all();
    }

    public function getUserById($id)
    {
        return $this->userRepository->findOrFail($id);
    }

    public function createUser(array $data)
    {
        try {
            DB::beginTransaction();

            // Hash password
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user = $this->userRepository->create($data);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create user: ' . $e->getMessage());
        }
    }

    public function updateUser($id, array $data)
    {
        try {
            DB::beginTransaction();

            // Hash password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user = $this->userRepository->update($id, $data);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to update user: ' . $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        try {
            DB::beginTransaction();

            $this->userRepository->delete($id);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to delete user: ' . $e->getMessage());
        }
    }

    public function searchUser($keyword)
    {
        return $this->userRepository->search($keyword);
    }

    public function getUsersByRole($role)
    {
        return $this->userRepository->getByRole($role);
    }

    public function getAdmins()
    {
        return $this->userRepository->getAdmins();
    }

    public function getManajers()
    {
        return $this->userRepository->getManajers();
    }

    public function getStaffs()
    {
        return $this->userRepository->getStaffs();
    }
}
