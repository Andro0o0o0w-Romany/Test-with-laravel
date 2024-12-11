<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Carbon\Carbon;

class ForceDeleteOldPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:force-delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force delete all softly-deleted posts older than 30 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = Carbon::now()->subDays(30);
        $posts = Post::onlyTrashed()->where('deleted_at', '<', $date)->get();

        foreach ($posts as $post) {
            $post->forceDelete();
        }

        $this->info('Old softly-deleted posts have been force-deleted successfully.');

        return 0;
    }
}
