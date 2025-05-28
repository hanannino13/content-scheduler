<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Platform;
use Illuminate\Support\Facades\DB; 

class AnalyticsController extends Controller
{
    /**
     * Define middleware for this controller.
     */
    public static function middleware(): array
    {
        return [
            'auth:sanctum', // Ensure user is authenticated
        ];
    }

    /**
     * Get overall post status counts for the authenticated user.
     * GET /api/analytics/post-status-counts
     */
    public function getPostStatusCounts(Request $request)
    {
        $userId = Auth::id();

        $statusCounts = Post::where('user_id', $userId)
                            ->select('status', DB::raw('count(*) as count'))
                            ->groupBy('status')
                            ->pluck('count', 'status')
                            ->toArray();

        $allStatuses = ['draft', 'scheduled', 'publishing', 'published', 'failed', 'partially_published'];
        foreach ($allStatuses as $status) {
            if (!isset($statusCounts[$status])) {
                $statusCounts[$status] = 0;
            }
        }

        return response()->json($statusCounts);
    }

    /**
     * Get publishing success/failure rates per platform for the authenticated user.
     * GET /api/analytics/platform-success-rates
     */
    public function getPlatformSuccessRates(Request $request)
    {
        $userId = Auth::id();

        $platformStats = DB::table('post_platform')
                            ->join('posts', 'post_platform.post_id', '=', 'posts.id')
                            ->join('platforms', 'post_platform.platform_id', '=', 'platforms.id')
                            ->where('posts.user_id', $userId)
                            ->select(
                                'platforms.name as platform_name',
                                DB::raw('COUNT(*) as total_attempts'),
                                DB::raw("SUM(CASE WHEN post_platform.platform_status = 'published' THEN 1 ELSE 0 END) as published_count"),
                                DB::raw("SUM(CASE WHEN post_platform.platform_status = 'failed' THEN 1 ELSE 0 END) as failed_count"),
                                DB::raw("SUM(CASE WHEN post_platform.platform_status = 'pending' THEN 1 ELSE 0 END) as pending_count") // Include pending for completeness
                            )
                            ->groupBy('platforms.name')
                            ->get();

        $formattedStats = $platformStats->map(function ($stat) {
            $successRate = ($stat->total_attempts > 0) ? round(($stat->published_count / $stat->total_attempts) * 100, 2) : 0;
            $failureRate = ($stat->total_attempts > 0) ? round(($stat->failed_count / $stat->total_attempts) * 100, 2) : 0;
            return [
                'platform_name' => $stat->platform_name,
                'total_attempts' => $stat->total_attempts,
                'published_count' => $stat->published_count,
                'failed_count' => $stat->failed_count,
                'pending_count' => $stat->pending_count,
                'success_rate' => $successRate,
                'failure_rate' => $failureRate,
            ];
        });

        return response()->json($formattedStats);
    }
}