<?php

namespace App\Http\Controllers\Api\V1\Providers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Providers\ProviderRequest;
use App\Http\Resources\Api\V1\Providers\ProviderCollection;
use App\Http\Resources\Api\V1\Providers\ProviderResource;
use App\Models\V1\ProviderModel;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\TranslationTrait;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;

class ProviderController extends Controller
{
    use HttpResponse;
    use UserTrait;
    use TranslationTrait;
    use StringTrait;

    public function index()
    {
        return $this->resourceResponse(
            new ProviderCollection(ProviderModel::where('user_id', $this->getSubUsersForAuthenticatedUser())->get())
        );
    }

    public function show(ProviderModel $provider)
    {
        if (in_array($provider->user_id, $this->getSubUsersForAuthenticatedUser())) {
            return $this->resourceResponse(
                new ProviderResource($provider)
            );
        }

        return $this->notFoundResponse($this->translateErrorMessage('provider', 'not_exists'));
    }

    /**
     * Summary of store.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProviderRequest $request)
    {
        $name = $this->strLimit($request->input('name'), 50);
        if (!ProviderModel::where('name', $name)->whereIn('user_id', $this->getSubUsersForAuthenticatedUser())->value('id')) {
            $provider = ProviderModel::create([
                'name' => $name,
                'user_id' => Auth::id(),
            ]);

            return $this->createdResponse(new ProviderResource($provider));
        }

        return $this->validation_errors(['provider' => $this->translateErrorMessage('provider', 'exists')]);
    }

    public function update(ProviderRequest $request, ProviderModel $provider)
    {
        $name = $this->strLimit($request->input('name'), 50);
        if (
            !ProviderModel::where('name', $name)
                ->whereIn('user_id', $this->getSubUsersForAuthenticatedUser())
                ->where('id', '!=', $provider->id)->first(['id'])
        ) {
            if ($name != $provider->name) {
                $provider->name = $name;
                $provider->update();

                return $this->success(new ProviderResource($provider), 'Provider Updated Successfully');
            }

            return $this->noContentResponse();
        }

        return $this->validation_errors(['provider' => $this->translateErrorMessage('provider', 'exists')]);
    }

    public function destroy(ProviderModel $provider)
    {
        if (in_array($provider->user_id, $this->getSubUsersForAuthenticatedUser())) {
            $provider->delete();

            return $this->success(null, 'Provider Deleted Successfully');
        }

        return $this->notFoundResponse('Provider not found');
    }
}
