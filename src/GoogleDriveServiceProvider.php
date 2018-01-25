<?php

namespace PrivateIT\FlySystem\GoogleDrive;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Google_Client;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        app()->extend(Google_Client::class, function ($client)
        {
            $config = config('filesystems.disks.googleDrive');
            /** @var Google_Client $client */
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);

            return $client;
        });

        app()->bind(GoogleSheetsPathManager::class, function ()
        {
            $config = config('filesystems.disks.googleDrive');
            /** @var Google_Client $client */
            $client       = app()->make(Google_Client::class);
            $sheetService = new \Google_Service_Sheets($client);
            $pathManager  = new GoogleSheetsPathManager($sheetService, $config['sheetId'], Storage::disk('local'));
            $pathManager->updateCacheFile();

            return $pathManager;
        });

        if ( ! app()->bound(PathManager::class)) {
            app()->bind(PathManager::class, GoogleSheetsPathManager::class);
        }

        Storage::extend('googleDrive', function ($app, $config)
        {
            /** @var Google_Client $client */
            $client = app()->make(Google_Client::class);
            /** @var PathManager $client */
            $pathManager = app()->make(PathManager::class);

            $gdService = new \Google_Service_Drive($client);
            $gdAdapter = new GoogleDriveAdapter($gdService, $config['folderId']);
            $gdAdapter->setPathManager($pathManager);

            return new \League\Flysystem\Filesystem($gdAdapter);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
