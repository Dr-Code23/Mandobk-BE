<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Dashboard\MonitorAndEvaluationRequest;
use App\Http\Resources\Api\V1\Dashboard\MonitorAndEvaluation\MonitorAndEvaluationCollection;
use App\Http\Resources\Api\V1\Dashboard\MonitorAndEvaluation\MonitorAndEvaluationResrouce;
use App\Models\User;
use App\Models\V1\Role;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MonitorAndEvaluationController extends Controller
{
    use HttpResponse;
    use StringTrait;
    use HttpResponse;
    use Translatable;
    use UserTrait;


    public function index()
    {
        // Monitor Collection Not Works Maybe because different models
        return $this->resourceResponse(
            new MonitorAndEvaluationCollection(
                User::join('roles', 'roles.id', 'users.role_id')
                    ->whereIn('roles.name', config('roles.monitor_roles'))
                    ->where('users.id', '!=', Auth::id())
                    ->select(
                        [
                            'users.id',
                            'users.full_name',
                            'users.username',
                            'users.role_id',
                            'roles.name as role_name',
                            'users.created_at',
                            'users.updated_at',
                        ]
                    )
                    ->get()
            )
        );
    }

    /**
     * Summary of store.
     *
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function store(MonitorAndEvaluationRequest $req)
    {
        $full_name = $this->sanitizeString($req->full_name);
        $username = $this->sanitizeString($req->username);

        // Check if role belong to monitor
        if ($role = Role::where('id', $req->role)->whereIn('name', config('roles.monitor_roles'))->first(['name'])) {
            // Store Data
            $user = User::create([
                'full_name' => $full_name,
                'username' => $username,
                'password' => $req->password,
                'role_id' => $req->role,
                'status' => '1'
            ]);
            $user->role_name = $role->name;

            return $this->success(new MonitorAndEvaluationResrouce($user), 'User Created Successfully');
        }

        return $this->validation_errors($this->translateErrorMessage('role', 'not_found'));
    }

    /**
     * Show One User.
     *
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function show(User $user)
    {
        if ($user->id != Auth::id()) {
            if ($role = Role::where('id', $user->role_id)->whereIn('name', config('roles.monitor_roles'))->first(['name'])) {
                $user->role_name = $role->name;

                return $this->resourceResponse(new MonitorAndEvaluationResrouce($user));
            }

            return $this->validation_errors($this->translateErrorMessage('role', 'not_found'));
        }

        return $this->error(null, msg: 'User ' . __('validation.not_found'));
    }

    /**
     * Summary of update.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MonitorAndEvaluationRequest $req, User $user)
    {
        if ($user->id != Auth::id()) {
            $role = Role::where('id', $user->role_id)->whereIn('name', config('roles.monitor_roles'))->first(['name as role_name', 'id']);

            if ($role) {
                $full_name = $this->sanitizeString($req->full_name);
                $username = $this->sanitizeString($req->username);

                $anyChangeOccured = false;
                if ($user->full_name != $full_name) {
                    $user->full_name = $full_name;
                    $anyChangeOccured = true;
                }
                if ($user->username != $username) {
                    $user->username = $username;
                    $anyChangeOccured = true;
                }
                if ($req->has('password')) {
                    if (!Hash::check($req->password, $user->password)) {
                        $user->password = $req->password;
                        $anyChangeOccured = true;
                    }
                }
                if ($user->role_id != $req->role) {
                    $user->role_id = $req->role;
                    $anyChangeOccured = true;
                }
                if ($anyChangeOccured) {
                    $user->update();
                    $user->role_name = $role->role_name;
                }
                return $this->success(new MonitorAndEvaluationResrouce($user), 'User Updated Successfully');
            }

            return $this->validation_errors($this->translateErrorMessage('role', 'not_found'));
        }

        return $this->notFoundResponse();
    }

    public function destroy(User $user)
    {
        if ($user->id != Auth::id()) {
            $user->load('role');
            if (in_array($user->role->name,  config('roles.monitor_roles'))) {
                $user->delete();

                return $this->success(msg: 'User Deleted Successfully');
            }
            return $this->validation_errors($this->translateErrorMessage('role', 'not_found'));
        }

        return $this->notFoundResponse();
    }
}
