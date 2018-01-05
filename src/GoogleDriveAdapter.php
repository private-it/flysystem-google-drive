<?php
/**
 * GoogleDriveAdapter.php.
 */

namespace PrivateIT\FlySystem;

use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter as OriginGoogleDriveAdapter;
use League\Flysystem\Config;

/**
 * Class GoogleDriveAdapter
 * @package PrivateIT\FlySystem
 */
class GoogleDriveAdapter extends OriginGoogleDriveAdapter
{
    /**
     * @var PathManager
     */
    public $pathManager;

    /**
     * @param PathManager $manager
     */
    public function setPathManager(PathManager $manager)
    {
        $this->pathManager = $manager;
    }

    /**
     * @param $path
     * @return string
     */
    public function replacePath($path)
    {
        if ($this->pathManager->readPath($path)) return $this->pathManager->readPath($path);

        $paths = explode('/', $path);
        $fileName = array_pop($paths);

        if ($fileName) {
            $fileId = $this->pathManager->readPath($path);
            $path = $fileName;
            if ($fileId) {
                $path = $fileId;
            }
        }

        $dirPath = implode('/', $paths);
        if ($dirPath) {
            $dirId = $this->pathManager->readPath($dirPath);
            if ($dirId) {
                $dirId = explode('/', $dirId);
                $dirId = array_pop($dirId);
                $path = $dirId . '/' . $path;
            } else {
                $path = $dirPath . '/' . $path;
            }
        }

        return $path;
    }

    /**
     * @param $path
     * @param $results
     * @return array|false
     */
    public function processPath($path, $results)
    {
        if ($results && isset($results['path'])) {
            if (!$this->pathManager->readPath($path)) {
                $fileId = explode('/', $results['path']);
                $fileId = array_pop($fileId);
                if ($path != $fileId) {
                    $this->pathManager->writePath($path, $fileId);
                }
            }
        }
        return $results;

    }

    /**
     * @inheritdoc
     */
    public function upload($path, $contents, Config $config)
    {
        return $this->processPath($path, parent::upload($this->replacePath($path), $contents, $config));
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        if (parent::delete($this->replacePath($path))) {
            return $this->pathManager->removePath($path);
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function has($path)
    {
        return parent::has($this->replacePath($path));
    }

    /**
     * @inheritdoc
     */
    public function createDir($path, Config $config)
    {
        while (sizeof($target = explode('/', $this->replacePath($path))) > 2) {
            $this->createDir($target[0] . '/' . $target[1], $config);
        }
        return $this->processPath($path, parent::createDir($this->replacePath($path), $config));
    }

    /**
     * @inheritdoc
     */
    public function getUrl($path)
    {
        return parent::getUrl($this->replacePath($path));
    }

    /**
     * @inheritdoc
     */
    public function read($path)
    {
        return parent::read($this->replacePath($path));
    }

    /**
     * @inheritdoc
     */
    public function readStream($path)
    {
        return parent::readStream($this->replacePath($path));
    }

    /**
     * @inheritdoc
     */
    public function listContents($dirname = '', $recursive = false)
    {
        return parent::listContents($dirname, $recursive);
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {
        return parent::getMetadata($this->replacePath($path));
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($path, $visibility)
    {
        return parent::setVisibility($this->replacePath($path), $visibility);
    }

    public function getVisibility($path)
    {
        return parent::getVisibility($this->replacePath($path));
    }

}