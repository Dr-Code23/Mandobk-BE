<?php

namespace App\Http\Controllers\Api\V1\Site\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Site\Pharmacy\SubUserRequest;
use App\Http\Resources\Api\V1\Site\Pharmacy\SubUsers\SubUsersCollection;
use App\Http\Resources\Api\V1\Site\Pharmacy\SubUsers\SubUsersResource;
use App\Models\User;
use App\Models\V1\Role;
use App\Models\V1\SubUser;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SubUsersController extends Controller
{
    use HttpResponse;

    public function index()
    {
        return $this->resourceResponse(
            new SubUsersCollection(
                SubUser::join('users', 'users.id', 'sub_users.sub_user_id')
                    ->where('sub_users.parent_id', Auth::id())
                    ->where('users.status', '1')
                    ->get([
                        'users.id as id',
                        'users.full_name as name',
                        'users.username as username',
                        'users.created_at as created_at',
                        'users.updated_at as updated_at',
                    ])
            )
        );
    }

    public function show(User $subuser)
    {
        if (
            SubUser::where('parent_id', Auth::id())
                ->where('sub_user_id', $subuser->id)
                ->value('id') && $subuser->status == '1'
        ) {
            return $this->resourceResponse(new SubUsersResource($subuser));
        }

        return $this->notFoundResponse('SubUser Not Found');
    }

    public function store(SubUserRequest $request)
    {
        // Create The User
        $user = User::create([
            'full_name' => $request->input('name'),
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
            'role_id' => Role::where('name', 'pharmacy_sub_user')->value('id'),
        ]);

        // Add SubUser To Pharmacy
        SubUser::create(['parent_id' => Auth::id(), 'sub_user_id' => $user->id]);

        return $this->createdResponse(new SubUsersResource($user));
    }

    public function update(SubUserRequest $request, User $subuser)
    {
        // Create The User
        if (SubUser::where('parent_id', Auth::id())->where('sub_user_id', $subuser->id)->value('id') && $subuser->status == '1') {
            $anyChangeOccur = false;
            if ($request->name != $subuser->full_name) {
                $subuser->full_name = $request->name;
                $anyChangeOccur = true;
            }
            if ($subuser->username != $request->username) {
                $subuser->username = $request->username;
                $anyChangeOccur = true;
            }
            if ($request->password && !Hash::check($request->password, $subuser->password)) {
                $subuser->password = Hash::make($request->password);
                $anyChangeOccur = true;
            }
            if ($anyChangeOccur) {
                $subuser->update();

                return $this->success(new SubUsersResource($subuser), 'SubUser Updated Successfully');
            }

            return $this->noContentResponse();
        }

        return $this->notFoundResponse('SubUser not found');
    }

    public function destroy(User $subuser)
    {
        if ($pharmacySubUser = SubUser::where('parent_id', Auth::id())->where('sub_user_id', $subuser->id)->value('id') && $subuser->status == '1') {
            $subuser->status = '0';
            $subuser->update();

            return $this->success(null, 'SubUser Deleted Successfully');
        }

        return $this->notFoundResponse('SubUser not found');
    }
}
