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
use App\Models\V1\Role;
use App\Models\V1\VisitorRecipe;
use App\Services\Api\V1\Users\UserService;
use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use Translatable;
    use HttpResponse;
    use UserTrait;
    use RoleTrait;


    public function __construct(
        private UserService $userService
    ){

    }
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

    /**
     * Register new Visitor
     * @param RegisterVisitorRequest $request
     * @return JsonResponse
     */
    public function registerNewVisitor(RegisterVisitorRequest $request): JsonResponse
    {
        return $this->resourceResponse(new VisitorAccountResource($this->userService->registerNewVisitor($request)));
    }

    /**
     * Restore Visitor Random Numbers
     * @param ForgotVisitorRandomNumberRequest $request
     * @return JsonResponse
     */
    public function forgotVisitorRandomNumber(ForgotVisitorRandomNumberRequest $request): JsonResponse
    {
        $randomNumbers = $this->userService->forgotVisitorRandomNumber($request);
        if(is_array($randomNumbers)){
            return $this->resourceResponse($randomNumbers);
        }
        return $this->notFoundResponse($this->translateErrorMessage('handle' , 'not_found'));
    }

    public function addRandomNumberForVistior(AddRandomNumberForVisitor $request)
    {
        $randomNumber = $this->userService->addRandomNumberForVistior($request);
        $visitor = User::where('username', $request->username)
            ->where('role_id', $this->getRoleIdByName('visitor'))
            ->first(['id', 'username']);

        $errors = [];
        if ($visitor) {
            // Search In Visitor Recipes For Visitor

            $recipe = VisitorRecipe::where('visitor_id', $visitor->id)
                ->where('alias', $request->alias)->first(['id', 'alias']);
            if (!$recipe) {
                $newVisitorRecipe = VisitorRecipe::create([
                    'visitor_id' => $visitor->id,
                    'alias' => $request->alias,
                    'random_number' => $this->generateRandomNumberForVisitor(),
                    'details' => []
                ]);

                return $this->resourceResponse(new VisitorRecipeResource($newVisitorRecipe));
            } else $errors['alias'] = ['Alias Already Exists'];
        } else $errors['username'] = ['Username Not Exists'];

        return $this->validation_errors($errors);
    }
    public function getHumanResourceUsers(UserService $userService)
    {
        $users = $userService->getHumanResourceUsers();
        return $this->resourceResponse($users);
    }
}
