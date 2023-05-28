<?php


namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use Storage;

class DOSpacesStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('do-spaces', function ($app, $config) {
            $client = new S3Client([
                'credentials' => [
                    'key' => $config["key"],
                    'secret' => $config["secret"]
                ],
                'region' => $config["region"],
                'version' => "latest",
                'bucket_endpoint' => false,
                'use_path_style_endpoint' => false,
                'endpoint' => $config["endpoint"],
                'url' => $config["url"]
            ]);
            return new Filesystem(new AwsS3V3Adapter($client, $config["bucket"]));
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
