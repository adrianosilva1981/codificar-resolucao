<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Refunds;
use Illuminate\Support\Facades\DB;

/**
 * Controller
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * get_refunds
     *
     * @param  mixed $year
     * @param  mixed $limit
     * @return void
     */
    public function get_refunds($year, $limit = 5) {
        $year = (int)$year;
        $limit = (int)$limit > 0 ? (int)$limit : 5;
        $limit = $limit > 1000 ? 1000 : $limit;

        $query = "SELECT SUM(r.`value`) AS `value`, d.`name`
                  FROM `refunds` r INNER JOIN `deputados` d ON r.`code` = d.`code`
                  WHERE r.`year` = ? GROUP BY d.`name`, r.`year` ORDER BY r.`value` DESC LIMIT ?";
        $refunds = DB::select($query, [$year, $limit]);

        return response()
            ->json([
                'results' => $refunds,
            ]);
    }

    /**
     * get_social_ranking
     *
     * @param  mixed $top
     * @return void
     */
    public function get_social_ranking($top = 5) {
        $top = (int)$top > 0 ? (int)$top : 5;
        $top = $top > 1000 ? 1000 : $top;

        $medias = DB::table('social_medias')
            ->join('deputados', 'social_medias.code', '=', 'deputados.code')
            ->select(DB::raw('count(*) as _count, social_medias.name', 'deputados.name'))
            ->groupBy('social_medias.name')
            ->orderBy('_count')
            ->get();

        return response()
            ->json([
                'results' => $medias,
            ]);

    }
}
