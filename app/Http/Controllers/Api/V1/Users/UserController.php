<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Users\ChangeUserStatusRequest;
use App\Http\Requests\Api\V1\Users\RegisterVisitorRequest;
use App\Http\Resources\Api\V1\Site\Doctor\VisitorAccount\VisitorAccountResource;
use App\Http\Resources\Api\V1\Users\UserCollection;
use App\Http\Resources\Api\V1\Users\UserResource;
use App\Models\User;
use App\Models\V1\Role;
use App\Models\V1\VisitorRecipe;
use App\Services\Api\V1\Users\UserService;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use Translatable;
    use HttpResponse;


    public function getAllUsersInDashboardToApprove(UserService $userService)
    {
        $users = $userService->getAllUsersInDashboardToApprove();
        return $this->resourceResponse(new UserCollection($users));
    }

    /**
     * Approve User For Admin
     *
     * @param ChangeUserStatusRequest $request
     * @param User $user
     * @param UserService $userService
     * @return JsonResponse
     */

    public function changeUserStatus(ChangeUserStatusRequest $request, User $user, UserService $userService): JsonResponse
    {
        $user = $userService->changeUserStatus($request, $user);

        //! Asserting that $user == true not working
        if (is_bool($user) && $user == true) {
            return $this->success(null, __('standard.deleted'));
        }
        if ($user != null) return $this->success(new UserResource($user));

        return $this->notFoundResponse('User not found');
    }

    /**
     * Get All Users For Select In Buying Process
     *
     * @param Request $request
     * @param UserService $userService
     * @return JsonResponse|array
     */
    public function getUsersForSelectBox(Request $request, UserService $userService): JsonResponse|array
    {

        $users = $userService->getUsersForSelectBox($request);

        if ($users != null) return $this->resourceResponse($users);

        return $this->notFoundResponse('No Users To Show');
    }

    public function registerNewVisitor(RegisterVisitorRequest $request)
    {
        $visitor = User::create($request->validated() + [
            'role_id' => Role::where('name', 'visitor')->value('id'),
            'full_name' => $request->name
        ]);

        $visitor_info = VisitorRecipe::create([
            'visitor_id' => $visitor->id,
            'alias' => $request->alias,
            'details' => [],
            'random_number' => $this->generateRandomNumberForVisitor(),
        ]);

        $visitor_info->name = $request->name;
        $visitor_info->username = $request->username;
        $visitor_info->phone = $request->phone;

        return $this->resourceResponse(new VisitorAccountResource($visitor_info));
    }

    public function ForgotVisitorRandomNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'handle' => ['required'],
        ], [
            'handle.required' => $this->translateErrorMessage('handle', 'required'),
        ]);
        if ($validator->fails()) {
            return $this->validation_errors($validator->errors());
        }
        $handle = $request->input('handle');
        if (
            $visitor = User::where(function ($query) use ($handle) {
                $query->where('username', $handle)
                    ->orWhere('phone', $handle);
            })->first(['id'])
        ) {
            $data = [];
            $cnt = 0;
            foreach (VisitorRecipe::where('visitor_id', $visitor->id)->get(['random_number', 'alias']) as $recipe) {
                $data[$cnt]['random_number'] = $recipe->random_number;
                $data[$cnt]['alias'] = $recipe->alias;
                ++$cnt;
            }

            return $this->resourceResponse($data);
        }

        return $this->notFoundResponse('There Is No User With That Handle');
    }
}
