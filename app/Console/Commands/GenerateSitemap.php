<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Carbon\Carbon;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Monster;

class GenerateSitemap extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // modify this to your own needs
        // SitemapGenerator::create(config('app.url'))
        //     ->writeToFile(public_path('sitemap.xml'));

            $sitemap = SitemapGenerator::create(config('app.url'))
                ->getSitemap();

            //Extra pages
            $sitemap = $sitemap->add(Url::create('/monstergrid')
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
                ->setPriority(0.1));    

            //Dynamically generated pages
            $monster_ids = Monster::where('status','complete')
                ->where('group_id','0')
                ->get()
                ->pluck('id');
            foreach ($monster_ids as $monster_id){
                $sitemap = $sitemap->add(Url::create('/monstergrid/'.$monster_id)
                    ->setLastModificationDate(Carbon::yesterday())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
                    ->setPriority(0.1));
            }

            $sitemap->writeToFile(public_path('sitemap.xml'));
    }
}