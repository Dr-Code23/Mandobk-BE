<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait paginationTrait{

    /**
     * Return The Pagination count if exists and valid or return 15
     * @param Request $request
     * @return int
     */
    public function paginateCount($request):int{

        if($cnt = $request->input('per_page')){
            if(is_numeric($cnt) && (int)$cnt > 15){
                return (int) $cnt;
            }
        }
        return 15;
    }
}
