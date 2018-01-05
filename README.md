# flysystem-google-drive
FlySystem adapter for Google Drive (work with path)

This project is wrapper of [nao-pon/flysystem-google-drive](https://github.com/nao-pon/flysystem-google-drive)

Read more information  [this](https://github.com/nao-pon/flysystem-google-drive)


## Installation

```bash
composer require private-it/flysystem-google-drive:dev-master
```

## Config

* [Getting your Client ID and Secret](https://github.com/ivanvermeyen/laravel-google-drive-demo/blob/master/README/1-getting-your-dlient-id-and-secret.md)
* [Getting your Refresh Token](https://github.com/ivanvermeyen/laravel-google-drive-demo/blob/master/README/2-getting-your-refresh-token.md)
* [Getting your Root Folder ID](https://github.com/ivanvermeyen/laravel-google-drive-demo/blob/master/README/3-getting-your-root-folder-id.md)


Config params:

```php
[
    'clientId' => 'xxxxxxxxxxx.apps.googleusercontent.com',
    'clientSecret' => 'xxxxxxxxxxx',
    'refreshToken' => 'xxxxxxxxxxx',
    'folderId' => 'xxxxxxxxxxx',
    'sheetId' => 'xxxxxxxxxxx',
];
```

**folderId** - uses as root folder for upload/read.

**sheetId** - file id of "Google Sheets" document. Uses for save path-fileId. This document must be shared for public read.


## Usage example

See `examples/test.php`

```bash
git clone https://github.com/private-it/flysystem-google-drive.git
composer install
```

Copy `examples/config.example.php` to `examples/config.php`

```bash
php examples/test.php
```

## Usage with Laravel

Set binding for default PathManager in file `bootstrap/app.php`

```php
$app->bind(\PrivateIT\FlySystem\GoogleDrive\PathManager::class, \PrivateIT\FlySystem\GoogleDrive\GoogleSheetsPathManager::class);
```

Add ServiceProvider to `config/app.php`

```php
'providers' => [
    ...
    PrivateIT\FlySystem\GoogleDrive\GoogleDriveServiceProvider::class
    ...
]
```

Configuration google drive `config/filesystems.php`

```php
    'disks' => [
...
        'googleDrive' => [
            'driver' => 'googleDrive',
            'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
            'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
            'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
            'folderId' => env('GOOGLE_DRIVE_FOLDER_ID'),
            'sheetId' => env('GOOGLE_DRIVE_PATH_MANAGER_SHEET_ID'),
        ],
...
    ],
```

Usage:

```php
        $disk = \Storage::disk('googleDrive');
        $adapter = $disk->getDriver()->getAdapter();

        $dir = 'test/222/333';
        $disk->makeDirectory($dir);
        $fileName = $dir . '/new-file-1.txt';

        var_dump($disk->put($fileName, '4444444444'));
        var_dump($disk->deleteDir($dir));
        var_dump($adapter->getUrl($fileName))
```
