<?php

namespace App\Http\Controllers\Api\V1\Archive;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Archive\MoveProductToArchiveRequest;
use App\Http\Resources\Api\V1\Archive\ArchiveCollection;
use App\Http\Resources\Api\V1\Archive\ArchiveResource;
use App\Models\V1\Archive;
use App\Models\V1\VisitorRecipe;
use App\Services\Api\V1\Archive\ArchiveService;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
    use HttpResponse;
    use Translatable;

    /**
     * Fetch All Products In Archive
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->resourceResponse(
            new ArchiveCollection(
                Archive::whereIn('random_number', function ($query) {
                    $query->select('random_number')
                        ->from(with(new VisitorRecipe())->getTable())
                        ->where('visitor_id', Auth::id());
                })
                    ->orderByDesc('updated_at')
                    ->get()
            )
        );
    }

    /**
     * Show one archive history for one user.
     *
     * @param Archive $archive
     * @return JsonResponse
     */
    public function show(Archive $archive): JsonResponse
    {
        $randomNumberExists = VisitorRecipe::where('visitor_id', auth()->id())->where('random_number', $archive->random_number)->first(['id']);
        if ($randomNumberExists) {
            return $this->resourceResponse(new ArchiveResource($archive));
        }

        return $this->notFoundResponse('This archive do not belong to authenticated user');
    }

    /**
     *  Move Products Associated With Random Number To Archive To Archive
     *
     * @param MoveProductToArchiveRequest $request
     * @param ArchiveService $archiveService
     * @return JsonResponse
     */
    public function moveProductsToArchive(MoveProductToArchiveRequest $request, ArchiveService $archiveService): JsonResponse
    {
        $archiveAdded = $archiveService->moveProductsToArchive($request);

        if (is_bool($archiveAdded) && $archiveAdded) {
            return $this->success(null, 'Products Moved To Archive Successfully');
        }

        return $this->validation_errors($archiveAdded);
    }

    /**
     * Remove All Details Associated With Random Number From Archive
     *
     * @param Archive $archive
     * @return JsonResponse
     */
    public function destroy(Archive $archive): JsonResponse
    {
        if (VisitorRecipe::where('visitor_id', Auth::id())->where('random_number', $archive->random_number)->first(['id'])) {
            $archive->delete();

            return $this->success(null, 'Archive Deleted Successfully');
        }

        return $this->notFoundResponse('This archive does not belong to authenticated user');
    }
}
