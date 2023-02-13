<?php

namespace App\Http\Controllers\Api\V1\Archive;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Archive\ArchiveCollection;
use App\Http\Resources\Api\V1\Archive\ArchiveResource;
use App\Models\V1\Archive;
use App\Models\V1\VisitorRecipe;
use App\Traits\HttpResponse;
use App\Traits\TranslationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ArchiveController extends Controller
{
    use HttpResponse;
    use TranslationTrait;

    /**
     * Show archive history for all associated visitor's users.
     *
     * @return array
     */
    public function index()
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
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function show(Archive $archive)
    {
        if (VisitorRecipe::where('visitor_id', Auth::id())->where('random_number', $archive->random_number)->first(['id'])) {
            return $this->resourceResponse(new ArchiveResource($archive));
        }

        return $this->notFoundResponse('This archive do not belong to authenticated user');
    }

    /**
     * Summary of moveFromRandomNumberProducts.
     *
     * @return \Illuminate\Http\JsonResponse|bool
     */
    public static function moveFromRandomNumberProducts(Request $request, int $randomNumber = null)
    {
        $archiveObject = new ArchiveController();
        $random_number = $randomNumber != null ? $randomNumber : $request->input('random_number');
        if ($randomNumber == null) {
            $validator = Validator::make($request->all(), [
                'random_number' => ['required', 'numeric'],
            ], [
                'random_number.required' => $archiveObject->translateErrorMessage('random_number', 'required'),
                'random_number.numeric' => $archiveObject->translateErrorMessage('random_number', 'numeric'),
            ]);
        }

        $visitor_recipe = VisitorRecipe::where('random_number', $random_number)->first(['id', 'details']);
        if ($visitor_recipe) {
            // updateVisitorDetails

            $visitor_details = $visitor_recipe->details;

            if ($randomNumber == null) {
                $visitor_recipe->details = [];
                $visitor_recipe->update();
            }

            $archive = Archive::where('random_number', $random_number)->first(['id', 'details']);
            if ($archive) {
                $archive_details = $archive->details;
                if (!$archive_details) {
                    $archive_details = [];
                }

                // ? To Prevent Apppending empty array
                if ($visitor_details) {
                    $archive_details[] = $visitor_details;
                }
                $archive->details = $archive_details;
                $archive->update();
                if ($randomNumber == null) {
                    return $archiveObject->createdResponse(null, 'Products Added To Archive Successfully');
                }

                return true;
            } else {
                Archive::create([
                    'random_number' => $random_number,
                    'details' => [],
                ]);
                if ($randomNumber == null) {
                    return $archiveObject->createdResponse(null, 'Products Added To Archive Successfully');
                }

                return true;
            }
        }
        if ($randomNumber == null) {
            return $archiveObject->validation_errors($validator->errors());
        }

        return false;
    }

    public function destroy(Archive $archive)
    {
        if (VisitorRecipe::where('visitor_id', Auth::id())->where('random_number', $archive->random_number)->first(['id'])) {
            $archive->delete();

            return $this->success(null, 'Archive Deleted Successfully');
        }

        return $this->notFoundResponse('This archive doesnot belong to authenticated user');
    }
}
