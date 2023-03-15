<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UserIndexRequest;
use App\Models\User;
use App\Http\Requests\Admin\User\UserStoreRequest;
use App\Http\Requests\Admin\User\UserUpdateRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\Role;
use App\Services\Admin\PasswordService;
use App\Services\Admin\UserService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * @var PasswordService
     */
    protected PasswordService $passwordService;

    /**
     * @param UserService $userService
     * @param PasswordService $passwordService
     */
    public function __construct(UserService $userService, PasswordService $passwordService)
    {
        $this->userService = $userService;
        $this->passwordService = $passwordService;
    }

    /**
     * @param UserIndexRequest $request
     * @return Response
     */
    public function index(UserIndexRequest $request): Response
    {
        Authorize::abortIfNot('user_index');

        $dataQuery = new DataQuery($request);

        $users = $this->userService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/User/Index', [
            'users' => UserResource::collection($users),
            'query' => $dataQuery->query(),

            'canUserStore' => Authorize::any('user_store'),
            'canUserEdit' => Authorize::any('user_update'),
            'canUserDestroy' => Authorize::any('user_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('user_store');

        return inertia('Admin/User/Create', [
            'roles' => Role::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * @param UserStoreRequest $userStoreRequest
     * @return RedirectResponse
     */
    public function store(UserStoreRequest $userStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('user_store');

        $user = $this->userService->store($userStoreRequest->validated());

        if (! $this->passwordService->sendCreatedMail($user)) {
            throw ValidationException::withMessages([
                'send_stored_user_mail' => 'Erro ao enviar email. Envie através da redefinição de senha.',
            ]);
        }

        return redirect()->route('admin.user.edit', $user);
    }

    /**
     * @param User $user
     * @return Response
     */
    public function edit(User $user): Response
    {
        Authorize::abortIfNot('user_update');

        return inertia('Admin/User/Edit', [
            'roles' => Role::select('id', 'name')->orderBy('name')->get(),
            'user' => new UserResource($user),
            'canUserDestroy' => Authorize::any('user_destroy'),
        ]);
    }

    /**
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        Authorize::abortIfNot('user_show');

        return inertia('Admin/User/Show', [
            'roles' => Role::select('id', 'name')->orderBy('name')->get(),
            'user' => new UserResource($user)
        ]);
    }

    /**
     * @param UserUpdateRequest $userUpdateRequest
     * @param User $user
     * @return RedirectResponse
     */
    public function update(UserUpdateRequest $userUpdateRequest, User $user): RedirectResponse
    {
        Authorize::abortIfNot('user_update');

        $user = $this->userService->update($user, $userUpdateRequest->validated());

        return redirect()->route('admin.user.index');
    }

    /**
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        Authorize::abortIfNot('user_destroy');

        $this->userService->delete($user);

        return redirect()->route('admin.user.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('user_destroy');

        $request->validate(['ids' => 'required|array|in:' . User::get()->pluck('id')->join(',')]);

        $this->userService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.user.index');
    }
}
