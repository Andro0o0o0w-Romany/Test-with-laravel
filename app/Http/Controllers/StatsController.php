<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    /**
     * Display the statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $totalUsers = User::count();
        $totalPosts = Post::count();
        $usersWithNoPosts = User::doesntHave('posts')->count();

        return response()->json([
            'total_users' => $totalUsers,
            'total_posts' => $totalPosts,
            'users_with_no_posts' => $usersWithNoPosts,
        ]);
    }
}
