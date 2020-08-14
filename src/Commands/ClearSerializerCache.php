<?php

namespace Cone\LaravelJMSSerializer\Commands;


use Cone\LaravelJMSSerializer\Contracts\DataNormalizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearSerializerCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'serializer:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears serializer metadata cache';

    /**
     * @var DataNormalizer
     */
    private $normalizer;


    public function __construct(DataNormalizer $normalizer)
    {
        parent::__construct();
        $this->normalizer = $normalizer;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dir = $this->normalizer->getCacheDir();
        foreach (File::files($dir) as $file) {
          File::delete($file->getPathname());
        }

        foreach (File::directories($dir) as $item) {
          File::deleteDirectory($item, true);
        }
        $this->info("Serializer Cache is cleared");
    }
}
