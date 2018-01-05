<?php
/**
 * PathManager.php.
 */

namespace PrivateIT\FlySystem;


interface PathManager
{
    /**
     * @param $path
     * @return mixed
     */
    public function readPath($path);

    /**
     * @param $path
     * @param $fileId
     * @return mixed
     */
    public function writePath($path, $fileId);

    /**
     * @param $path
     * @return mixed
     */
    public function removePath($path);

}