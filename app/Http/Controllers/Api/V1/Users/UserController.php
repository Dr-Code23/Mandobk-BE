<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Users\ChangeUserStatusRequest;
use App\Http\Requests\Api\V1\Users\ForgotVisitorRandomNumberRequest;
use App\Http\Requests\Api\V1\Users\RegisterVisitorRequest;
use App\Http\Requests\Api\V1\Visitor\AddRandomNumberForVisitor;
use App\Http\Resources\Api\V1\Site\Doctor\VisitorAccount\VisitorAccountResource;
use App\Http\Resources\Api\V1\Site\VisitorRecipe\VisitorRecipeResource;
use App\Http\Resources\Api\V1\Users\UserCollection;
use App\Http\Resources\Api\V1\Users\UserResource;
use App\Models\User;
use App\Models\V1\VisitorRecipe;
use App\Services\Api\V1\Users\UserService;
use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use Translatable, HttpResponse, UserTrait, RoleTrait;

    public function __construct(private UserService $userService)
    {
    }

    /**
     * Get Public Users In Dashboard To Manage
     * @return JsonResponse
     */
    public function getAllUsersToManage(): JsonResponse
    {
        return $this->resourceResponse(
            new UserCollection($this->userService->getAllUsersToManage())
        );
    }

    /**
     * Approve User For Admin
     *
     * @param ChangeUserStatusRequest $request
     * @param User $user
     * @return JsonResponse
     */

    public function changeUserStatus(ChangeUserStatusRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->changeUserStatus($request, $user);

        //! Asserting that $user == true not working
        if (is_bool($user) && $user) {

            return $this->success(null, __('standard.deleted'));
        }

        if ($user != null) {

            return $this->success(new UserResource($user));
        }

        return $this->notFoundResponse('User not found');
    }

    /**
     * Get All Users For Select In Buying Process
     *
     * @param Request $request
     * @return JsonResponse|array
     */
    public function getUsersForSelectBox(Request $request): JsonResponse|array
    {
        $users = $this->userService->getUsersForSelectBox($request);

        if ($users != null) {

            return $this->resourceResponse($users);
        }

        return $this->notFoundResponse('No Users To Show');
    }

    /**
     * Register new Visitor
     * @param RegisterVisitorRequest $request
     * @return JsonResponse
     */
    public function registerNewVisitor(RegisterVisitorRequest $request): JsonResponse
    {
        return $this->resourceResponse(
            new VisitorAccountResource($this->userService->registerNewVisitor($request))
        );
    }

    /**
     * Restore Visitor Random Numbers
     * @param ForgotVisitorRandomNumberRequest $request
     * @return JsonResponse
     */
    public function forgotVisitorRandomNumber(ForgotVisitorRandomNumberRequest $request): JsonResponse
    {
        $randomNumbers = $this->userService->forgotVisitorRandomNumber($request);

        if (is_array($randomNumbers)) {

            return $this->resourceResponse($randomNumbers);
        }

        return $this->notFoundResponse(
            $this->translateErrorMessage('handle', 'not_found')
        );
    }

    /**
     * Add Random Number For Visitor
     * @param AddRandomNumberForVisitor $request
     * @return JsonResponse
     */
    public function addRandomNumberForVisitor(AddRandomNumberForVisitor $request): JsonResponse
    {
        $randomNumber = $this->userService->addRandomNumberForVisitor($request);

        if ($randomNumber instanceof VisitorRecipe) {

            return $this->resourceResponse(
                new VisitorRecipeResource($randomNumber)
            );
        }

        return $this->validationErrorsResponse($randomNumber);
    }

    /**
     * Get Users For Human Resource
     * @return JsonResponse
     */
    public function getHumanResourceUsers(): JsonResponse
    {
        return $this->resourceResponse(
            $this->userService->getHumanResourceUsers()
        );
    }
}
