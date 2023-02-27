<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Dashboard\MonitorAndEvaluationRequest;
use App\Http\Resources\Api\V1\Dashboard\MonitorAndEvaluation\MonitorAndEvaluationCollection;
use App\Http\Resources\Api\V1\Dashboard\MonitorAndEvaluation\MonitorAndEvaluationResrouce;
use App\Models\User;
use App\Models\V1\Role;
use App\Services\Api\V1\Dashboard\MonitorAndEvaluationService;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MonitorAndEvaluationController extends Controller
{
    use HttpResponse;
    use StringTrait;
    use HttpResponse;
    use Translatable;
    use UserTrait;

    public function __construct(
        private MonitorAndEvaluationService $monitorService
    ) {
    }

    /**
     * Show All Users For Monitor
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->resourceResponse(new MonitorAndEvaluationCollection($this->monitorService->index()));
    }

    /**
     * Show One User.
     *
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $user = $this->monitorService->show($user);

        if ($user instanceof User) {

            return $this->resourceResponse(new MonitorAndEvaluationResrouce($user));
        } else if (isset($user['role'])) return $this->validation_errors($user);
        return $this->error($user);
    }



    /**
     * Store User For Admin
     *
     * @param MonitorAndEvaluationRequest $request
     * @return JsonResponse
     */
    public function store(MonitorAndEvaluationRequest $request): JsonResponse
    {
        $newUser = $this->monitorService->store($request);
        if ($newUser instanceof User) {
            return $this->success(new MonitorAndEvaluationResrouce($newUser), $this->translateSuccessMessage('user', 'created'));
        }

        return $this->validation_errors($newUser);
    }



    /**
     * Update User
     *
     * @return JsonResponse
     */
    public function update(MonitorAndEvaluationRequest $req, User $user): JsonResponse
    {
        $user = $this->monitorService->update($req, $user);

        if ($user instanceof User) {
            return $this->success(new MonitorAndEvaluationResrouce($user), 'User Updated Successfully');
        }

        return $this->notFoundResponse(msg: $user);
    }

    /**
     * Delete User
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $deleted = $this->monitorService->destroy($user);
        if (is_bool($deleted) && $deleted) {
            return $this->success(msg: $this->translateSuccessMessage('user', 'deleted'));
        }

        return $this->notFoundResponse(msg: $deleted);
    }
}
