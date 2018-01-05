<?php
/**
 * LocalPathManager.php.
 */

namespace PrivateIT\FlySystem;

use Illuminate\Filesystem\FilesystemAdapter;

/**
 * Class LocalPathManager
 * @package PrivateIT\FlySystem
 */
class GoogleSheetsPathManager implements PathManager
{
    /**
     * @var \Google_Service_Sheets
     */
    public $service;

    /**
     * @var string
     */
    public $spreadsheetId;

    /**
     * @var FilesystemAdapter
     */
    public $storage;

    /**
     * @var string
     */
    public $cacheFile = 'google-drive-paths.csv';

    /**
     * @var array
     */
    public $cache = [];

    public function __construct(\Google_Service_Sheets $service, $spreadsheetId, FilesystemAdapter $storage)
    {
        $this->service = $service;
        $this->spreadsheetId = $spreadsheetId;
        $this->storage = $storage;
    }

    public function readPath($path)
    {
        if (isset($this->cache[$path])) return $this->cache[$path];

        $result = $this->findPath($path);
        if (isset($result[0]['fileId'])) {
            return $this->cache[$path] = $result[0]['fileId'];
        }
        return false;
    }

    public function findPath($path, $asDir = false)
    {
        $output = [];
        exec('grep -ni ",' . $path . ($asDir ? '' : ',') . '" ' . $this->storage->path($this->cacheFile), $output);
        if (!empty($output) && sizeof($output)) {
            $results = [];
            foreach ($output as $item) {
                list($index, $path, $fileId) = explode(',', $output[0]);
                $index = explode(':', $index);
                $index = array_shift($index);
                $results[] = compact('index', 'path', 'fileId');
            }
            return $results;
        }
        return false;
    }

    public function writePath($path, $fileId)
    {
        $range = 'A1:C1';
        $response = $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            $range,
            new \Google_Service_Sheets_ValueRange(
                [
                    "range" => $range,
                    "majorDimension" => "ROWS",
                    'values' => [
                        ['#', $path, $fileId]
                    ]
                ]
            ),
            [
                "valueInputOption" => "USER_ENTERED",
            ]
        );
        if (!isset($response->updates)) {
            return false;
        }
        if (!isset($response->updates->updatedRows)) {
            return false;
        }
        if ($response->updates->updatedRows != 1) {
            return false;
        }
        return $this->updateCacheFile();
    }

    public function removePath($path)
    {
        $results = $this->findPath($path, true);
        if (!$results) return null;

        foreach ($results as $result) {
            $response = $this->service->spreadsheets->batchUpdate($this->spreadsheetId,
                new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                    'requests' =>
                        [[
                            'deleteDimension' => [
                                'range' => [
                                    'sheetId' => 0,
                                    'dimension' => 'ROWS',
                                    'startIndex' => $result['index'] - 1,
                                    'endIndex' => $result['index']
                                ]
                            ]
                        ]]

                ])
            );
            if (!$response) {
                return false;
            }
        }
        return $this->updateCacheFile();
    }

    public function updateCacheFile()
    {
        return $this->storage->put(
            $this->cacheFile,
            file_get_contents(
                'https://docs.google.com/spreadsheets/u/0/d/' . $this->spreadsheetId .
                '/export?format=csv&id=' . $this->spreadsheetId .
                '&gid=0'
            )
        );
    }
}

