<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Dashboard\MonitorAndEvaluationRequest;
use App\Http\Resources\Api\V1\Dashboard\MonitorAndEvaluation\MonitorAndEvaluationCollection;
use App\Http\Resources\Api\V1\Dashboard\MonitorAndEvaluation\MonitorAndEvaluationResrouce;
use App\Models\User;
use App\Services\Api\V1\Dashboard\MonitorAndEvaluationService;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;

class MonitorAndEvaluationController extends Controller
{
    use HttpResponse, StringTrait, HttpResponse, Translatable, UserTrait;

    /**
     * @var MonitorAndEvaluationService
     */
    protected MonitorAndEvaluationService $monitorService;

    /**
     * @param MonitorAndEvaluationService $monitorService
     */
    public function __construct(MonitorAndEvaluationService $monitorService)
    {
        $this->monitorService = $monitorService;
    }

    /**
     * Show All Users For Monitor
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return
            $this->resourceResponse(new MonitorAndEvaluationCollection($this->monitorService->index()));
    }

    /**
     * Show One User.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $user = $this->monitorService->show($user);

        if ($user instanceof User) {
            return $this->resourceResponse(new MonitorAndEvaluationResrouce($user));

        } elseif (isset($user['role'])) {
            return $this->validation_errors($user);
        }

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
            return $this->success(
                new MonitorAndEvaluationResrouce($newUser),
                $this->translateSuccessMessage('user', 'created')
            );
        }

        return $this->validation_errors($newUser);
    }

    /**
     * Update User
     *
     * @param MonitorAndEvaluationRequest $req
     * @param User $user
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
