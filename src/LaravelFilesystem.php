<?php

namespace PrivateIT\FlySystem\GoogleDrive;

use Illuminate\Contracts\Filesystem\Filesystem;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;

/**
 * Class LaravelFilesystem
 *
 * Implementation of Illuminate\Contracts\Filesystem\Filesystem
 *
 * @package PrivateIT\FlySystem\GoogleDrive
 */
class LaravelFilesystem extends \League\Flysystem\Filesystem implements Filesystem
{
    /**
     * @inheritdoc
     */
    public function put($path, $contents, $config = []): bool
    {
        return parent::put($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function exists($path): bool
    {
        return parent::has($path);
    }

    /**
     * @inheritdoc
     * @throws FileNotFoundException
     */
    public function prepend($path, $data): bool
    {
        return parent::update($path, $data);
    }

    /**
     * @inheritdoc
     * @throws FileNotFoundException
     */
    public function append($path, $data): bool
    {
        return parent::update($path, $data);
    }

    /**
     * @inheritdoc
     * @throws FileNotFoundException
     * @throws FileExistsException
     */
    public function move($from, $to): bool
    {
        return parent::copy($from, $to) && $this->delete($from);
    }

    /**
     * @inheritdoc
     * @throws FileNotFoundException
     */
    public function size($path)
    {
        return parent::getSize($path);
    }

    /**
     * @inheritdoc
     * @throws FileNotFoundException
     */
    public function lastModified($path)
    {
        return parent::getTimestamp($path);
    }

    /**
     * @inheritdoc
     */
    public function files($directory = null, $recursive = false): array
    {
        return parent::listFiles($directory, $recursive);
    }

    /**
     * @inheritdoc
     */
    public function allFiles($directory = null): array
    {
        return parent::listFiles($directory, true);
    }

    /**
     * @inheritdoc
     */
    public function directories($directory = null, $recursive = false): array
    {
        return parent::listPaths($directory, $recursive);
    }

    /**
     * @inheritdoc
     */
    public function allDirectories($directory = null): array
    {
        return parent::listPaths($directory, true);
    }

    /**
     * @inheritdoc
     */
    public function makeDirectory($path): bool
    {
        return parent::createDir($path);
    }

    /**
     * @inheritdoc
     */
    public function deleteDirectory($directory): bool
    {
        return parent::deleteDir($directory);
    }
}