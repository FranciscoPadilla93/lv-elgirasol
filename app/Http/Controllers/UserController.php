<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\IndexUserRequest;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index(IndexUserRequest  $request): JsonResponse
    {
        $search = $request->string('search')->toString();
        $perPage = $request->integer('per_page', 15);
        $page = $request->integer('page', 1);
        $users = $this->getUsersFromDatabase($search, $perPage, $page);

        return ResponseHelper::success(
            UserResource::collection($users),
            'Usuarios obtenidos correctamente.'
        );
    }

    protected function getUsersFromDatabase(string $search, int $perPage, int $page)
    {
        return User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $this->userService->create($data);

        return ResponseHelper::success(
            new UserResource($user),
            'Usuario creado correctamente.',
            201
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        $user = $this->userService->update($user, $data);

        return ResponseHelper::success(
            new UserResource($user),
            'Usuario actualizado correctamente.'
        );
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return ResponseHelper::success(
            null,
            'Usuario eliminado correctamente.'
        );
    }

    public function restore($id)
    {
        $user = $this->userService->restore($id);

        if (!$user) {
            return ResponseHelper::error(
                'Usuario no encontrado o no está eliminado',
                404
            );
        }

        $user->restore();

        return ResponseHelper::success(
            new UserResource($user),
            'Usuario restaurado correctamente'
        );
    }
}
