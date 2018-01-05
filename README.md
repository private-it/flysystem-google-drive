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


**sheetId** - file id of "Google Sheets" document

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
$app->bind(\PrivateIT\FlySystem\PathManager::class, \PrivateIT\FlySystem\GoogleSheetsPathManager::class);
```

Add ServiceProvider to `config/app.php`

```php
'providers' => [
    ...
    PrivateIT\FlySystem\GoogleDriveServiceProvider::class
    ...
]
```
