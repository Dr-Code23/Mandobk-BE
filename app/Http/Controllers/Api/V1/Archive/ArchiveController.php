<?php

namespace App\Http\Controllers\Api\V1\Archive;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Archive\ArchiveCollection;
use App\Http\Resources\Api\V1\Archive\ArchiveResource;
use App\Models\V1\Archive;
use App\Models\V1\VisitorRecipe;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ArchiveController extends Controller
{
    use HttpResponse;
    use Translatable;

    /**
     * Undocumented function.
     *
     * @return mixed
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
        $randomNumberExists = VisitorRecipe::where('visitor_id', auth()->id())->where('random_number', $archive->random_number)->first(['id']);
        if ($randomNumberExists) {
            return $this->resourceResponse(new ArchiveResource($archive));
        }

        return $this->notFoundResponse('This archive do not belong to authenticated user');
    }

    /**
     * Summary of moveFromRandomNumberProducts.
     *
     * @return \Illuminate\Http\JsonResponse|bool
     */
    public function moveFromRandomNumberProducts(Request $request)
    {

        $random_number = $request->input('random_number');

        $validator = Validator::make($request->all(), [
            'random_number' => ['required', 'numeric'],
        ], [
            'random_number.required' => $this->translateErrorMessage('random_number', 'required'),
            'random_number.numeric' => $this->translateErrorMessage('random_number', 'numeric'),
        ]);

        if ($validator->fails()) return $this->validation_errors($validator->errors());

        $visitor_recipe = VisitorRecipe::where('visitor_id', auth()->id())
            ->where('random_number', $random_number)
            ->first(['id', 'details']);

        // return $visitor_recipe;
        if ($visitor_recipe) {
            // updateVisitorDetails

            // $visitor_details = $visitor_recipe->details;

            $archiveDetails = [];
            $archive = Archive::where('random_number', $random_number)->first(['id', 'details']);
            // return $archive;
            $visitorDetails = array_merge([], $visitor_recipe->details);
            if ($visitorDetails) $archiveDetails[] = $visitorDetails;
            if ($archive) {
                $archiveDetails = array_merge($archiveDetails, $archive->details);
            }
            if ($archive) {
                if ($visitorDetails)
                    $archive->update([
                        'details' => $archiveDetails
                    ]);
            } else {
                Archive::create([
                    'random_number' => $random_number,
                    'details' => $archiveDetails,
                ]);
            }
            if ($visitorDetails) {
                $visitor_recipe->update(['details' => []]);
            }

            return $this->success(null, 'Products Moved To Archive Successfully');
        }
        return $this->validation_errors(['random_number' => ['Random Number Not Exists']]);
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
