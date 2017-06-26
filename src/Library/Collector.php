<?php
/**
 * Created by PhpStorm.
 * User: jeremydillenbourg
 * Date: 26/06/2017
 * Time: 09:36
 */

namespace Famousinteractive\ContentApi\Library;

/**
 * This class will manage a collector in the project in order to extract all the fitrans() reference.
 * Class Collector
 * @package Famousinteractive\ContentApi\Library
 */
class Collector
{
    protected $directory = null;
    protected $extensionToScan = [];
    protected $files = [];
    protected $collects = [];
    protected $transList = [];

    /**
     * Collector constructor.
     * @param $directory
     * @param array $extensionToScan
     */
    public function __construct($directory, $extensionToScan = [])
    {
        $this->directory = $directory;
        $this->extensionToScan = $extensionToScan;

        $this->getFilesRecursivly($directory);

        return $this;
    }

    /**
     * @return array
     */
    public function collect() {

        foreach($this->files as $file) {

            if(($fileContent = file_get_contents($file)) !== FALSE) {

                preg_match_all('#fitrans\((.*)\)#', $fileContent, $matches, PREG_SET_ORDER);

                foreach($matches as $match) {
                    $this->collects[$file][] = [
                        'success'   => true,
                        'trans'     => $match[0],
                        'isVariable'=> $this->isVariable($this->getCleanKeyFromArgument($match[1])),
                        'key'       => $this->getCleanKeyFromArgument($match[1]),
                        'default'   => isset(explode(',',$match[1])[3]) ? explode(',',$match[1])[3] : $this->getCleanKeyFromArgument($match[1])
                    ];
                }
            } else {
                $this->collects[$file][] = [
                    'success'   => false,
                    'message'   => 'file '.$file.' not readable'
                ];
            }
        }
        return $this->collects;
    }

    /**
     * @return bool|int
     */
    public function push() {

        $api = Api::getApi();
        $success = 1;

        foreach($this->getTransList() as $trans) {
            $key = $trans['key'];
            $default = $trans['default'];

            foreach (config('famousContentApi.lang') as $language) {
                $translationCount = $api->getBy($key, $language);
                if (empty($translationCount)) {
                    $success *= $api->pushMissingTranslation($key, $language, $default);
                }
            }
        }

        return $success;
    }

    /**
     * @param $directory
     * @return $this
     */
    protected function getFilesRecursivly($directory) {

        $filesAndDirs = array_slice(scandir($directory),2);

        foreach($filesAndDirs as $fileOrDir) {
            $currentPath = $directory.'/'.$fileOrDir;
            $extension = substr($currentPath, strrpos($currentPath, '.')+1);

            if(is_dir($currentPath)) {
                $this->getFilesRecursivly($currentPath);
            } elseif(in_array($extension, $this->extensionToScan)) {
                $this->files[] = $currentPath;
            }
        }
        return $this;
    }

    /**
     * @param $argument
     * @return string
     */
    protected function getCleanKeyFromArgument($argument) {
        $key = explode(',',$argument)[0];

        if($this->isVariable($key)) {
            return $key;
        } else {
            $quotingUsed = substr($key, 0, 1);
            $key = trim($key, $quotingUsed);
            $key = rtrim($key, $quotingUsed);
        }
        return $key;
    }

    /**
     * @param $key
     * @return bool
     */
    protected function isVariable($key) {
        return (strpos($key, '$') !== FALSE);
    }

    /**
     * @param $key
     * @param string $default
     */
    public function setTransList($key, $default='') {
        $this->transList[] = [
            'key'   => $key,
            'default'   => !empty($default) ? $default : $key
        ];
    }

    /**
     * @return array
     */
    public function getTransList() {
        return $this->transList;
    }

}