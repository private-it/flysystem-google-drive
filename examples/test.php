<?php
require __DIR__ . '/../vendor/autoload.php';

use PrivateIT\FlySystem\GoogleDrive\GoogleSheetsPathManager;
use PrivateIT\FlySystem\GoogleDrive\GoogleDriveAdapter;

use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Adapter\Local as LocalAdapter;

$localStorage = new FilesystemAdapter(
    new Flysystem(
        new LocalAdapter(__DIR__ . '/storage')
    )
); // or Store::disk('local')


$config = require __DIR__ . '/config.php';
$sheetId = $config['sheetId'];

$client = new \Google_Client();
$client->setClientId($config['clientId']);
$client->setClientSecret($config['clientSecret']);
$client->refreshToken($config['refreshToken']);


// Example for saved file paths
$sheetService = new \Google_Service_Sheets($client);
$pathManager = new GoogleSheetsPathManager($sheetService, $sheetId, $localStorage);
$pathManager->updateCacheFile();

// Use Google Drive adapter
$gdService = new \Google_Service_Drive($client);
$gdAdapter = new GoogleDriveAdapter($gdService, $config['folderId']);
$gdAdapter->setPathManager($pathManager);


$emptyConfig = new \League\Flysystem\Config();

$dir = 'test/222/333';
$fileName = $dir . '/new-file-1.txt';

echo 'Open for watch:' . PHP_EOL;
echo $gdAdapter->getUrl($sheetId);
echo PHP_EOL;
echo 'Document must be publish for reading.' . PHP_EOL;
echo PHP_EOL;

echo '1. Create folder' . PHP_EOL;
var_dump($gdAdapter->createDir($dir, $emptyConfig));
echo PHP_EOL;

echo '2. Create file' . PHP_EOL;
var_dump($gdAdapter->write($fileName, 'Hello world!', $emptyConfig));
echo PHP_EOL;

echo '3. Delete file' . PHP_EOL;
var_dump($gdAdapter->delete($fileName));
echo PHP_EOL;

echo '4. Delete folder' . PHP_EOL;
var_dump($gdAdapter->deleteDir($dir));
echo PHP_EOL;
