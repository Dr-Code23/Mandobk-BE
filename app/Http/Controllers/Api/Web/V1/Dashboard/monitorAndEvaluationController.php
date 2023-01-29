<?php

namespace App\Http\Controllers\Api\Web\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Dashboard\monitorAndEvaluationRequest;
use App\Http\Resources\Api\Web\V1\Dashboard\MonitorAndEvaluation\monitorAndEvaluationCollection;
use App\Http\Resources\Api\Web\V1\Dashboard\MonitorAndEvaluation\monitorAndEvaluationResrouce;
use App\Models\Api\Web\V1\Role;
use App\Models\User;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\translationTrait;
use App\Traits\userTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class monitorAndEvaluationController extends Controller
{
    use HttpResponse;
    use StringTrait;
    use HttpResponse;
    use translationTrait;
    use userTrait;

    /**
     * Get Translated Content
     * @return array
     */
    public function lang_content()
    {
        return $this->resourceResponse($this->getWebTranslationFile('Dashboard/monitorAndEvaluationTranslationFile'));
    }

    /**
     * List All Users To Monitor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->success(new monitorAndEvaluationCollection(User::join(config('roles.table_name'), config('roles.table_name').'.id', 'users.role_id')
            ->whereIn(config('roles.table_name').'.name', config('roles.monitor_roles'))
            ->where('id' , '!=' , $this->getAuthenticatedUserId())
            ->select(['users.*', config('roles.table_name').'.name as role_name'])
            ->get()
        )
        );
    }

    /**
     * Summary of store.
     *
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function store(monitorAndEvaluationRequest $req)
    {
        $full_name = $this->sanitizeString($req->full_name);
        $username = $this->sanitizeString($req->username);

        // Check if role belong to monitor
        if ($role = Role::where('id', $req->role)->whereIn('name', config('roles.monitor_roles'))->first(['name'])) {
            // Store Data
            $user = User::create([
                'full_name' => $full_name,
                'username' => $username,
                'password' => Hash::make($req->password),
                'role_id' => $req->role,
            ]);
            $user->role_name = $role->name;

            return $this->success(new monitorAndEvaluationResrouce($user), 'User Created Successfully');
        }

        return $this->validation_errors($this->translateErrorMessage('Dashboard/monitorAndEvaluationTranslationFile.role', 'not_found'));
    }

    /**
     * Show One User.
     *
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function show(User $user)
    {
        if ($role = Role::where('id', $user->role_id)->whereIn('name', config('roles.monitor_roles'))->where('id' , '!=' , $this->getAuthenticatedUserId())->first(['name'])) {
            $user->role_name = $role->name;

            return $this->resourceResponse(new monitorAndEvaluationResrouce($user));
        }
        // throw new NotFoundHttpException;
        return $this->error(null, msg: 'User '.__('validation.not_found'));
    }

    /**
     * Summary of update.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(monitorAndEvaluationRequest $req, User $user)
    {
        if ($role = Role::where('id', $user->role_id)->whereIn('name', config('roles.monitor_roles'))->where('id' , '!=' , $this->getAuthenticatedUserId())->first(['name'])) {
            $full_name = $this->sanitizeString($req->full_name);
            $username = $this->sanitizeString($req->username);
            $user->full_name = $full_name;
            $user->username = $username;
            if ($req->has('password')) {
                $user->password = Hash::make($req->password);
            }
            $user->update();
            $user->role_name = $role->name;

            return $this->success(new monitorAndEvaluationResrouce($user), 'User Updated Successfully');
        }

        return $this->validation_errors($this->translateErrorMessage('Dashboard/monitorAndEvaluationTranslationFile.role', 'not_found'));
    }


    public function destroy(User $user)
    {
        if (Role::where('id', $user->role_id)->whereIn('name', config('roles.monitor_roles'))->where('id' , '!=' , $this->getAuthenticatedUserId())->first(['name'])) {
            $user->delete();
            return $this->success(msg: 'User Deleted Successfully');
        }
        return $this->error(msg: 'User ' . __('validation.not_found'));
    }
}
