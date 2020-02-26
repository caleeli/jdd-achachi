<?php

namespace JDD\Achachi\Builder;

/**
 * Description of FolderTemplate
 *
 * @author davidcallizaya
 */
class FolderTemplate
{

    private $sourcePath;

    /**
     * Por ejemplo:
     * 
     * $name = 'david'
     * $ciudades = ['lapaz', 'cochabamba']
     * 
     * $name.php -> Crea un archivo de nombre: david.php
     * $ciudades.json -> Crea los archivos: lapaz.json cochabamba.json
     * 
     * Se conserva la extension del archivo con la finalidad de que el editor
     * de texto reconozca el formato original
     */
    public function __construct($sourcePath, callable $transformer = null)
    {
        $this->sourcePath = trim($sourcePath);
        $this->transformer = $transformer;
        if (!$this->sourcePath) {
            throw new \RuntimeException('Missing source path');
        }
    }

    public function build($data, $target)
    {
        $this->data = $data;
        $this->buildFolder($this->sourcePath, $target, [], $this->transformer);
    }

    private function buildFolder($path, $target, $keys0, callable $transformer = null)
    {
        if (!file_exists($target)) {
            mkdir($target, fileperms($path));
        }
        if (file_exists("$path/_.php")) {
            $transformer = require("$path/_.php");
        }
        foreach (glob($path . '/*') as $filename) {
            if (basename($filename) === '_.php') {
                continue;
            }
            $keys = $keys0;
            list($subkey, $names, $isVariable) = $this->resolveName($filename, $keys,
                $transformer);
            if ($subkey) {
                $keys[] = $subkey;
            }
            foreach ($names as $index => $name) {
                $forKeys = $keys;
                $isVariable ? $forKeys[] = $index : null;
                if (is_file($filename)) {
                    $this->buildFile($filename, $target . '/' . $name, $forKeys,
                        $transformer);
                } else {
                    $this->buildFolder($filename, $target . '/' . $name, $forKeys,
                        $transformer);
                }
            }
        }
    }

    private function buildFile($filename, $target, $keys, callable $transformer = null)
    {
        $template = new NewCommentTemplate(file_get_contents($filename),
            $transformer);
        $data = $keys ? array_get($this->data, implode('.', $keys)) : $this->data;
        $data = array_merge($this->data, $data);
        $data = $transformer ? array_merge($data, $transformer($data)) : $data;
        $res = $template->evaluate($data);
        file_put_contents($target, $res);
        chmod($target, fileperms($filename));
    }

    /**
     * $array->property.php
     *
     * @param type $filename
     * @param array $keys
     * @param \JDD\Achachi\Builder\callable $transformer
     * @return type
     */
    private function resolveName($filename, array $keys, callable $transformer = null)
    {
        $parts = explode('.', basename($filename), 2);
        $e = substr($parts[0], 0, 1);
        if ($e === '$') {
            $nameParts = explode('->', $parts[0]);
            $key = substr($nameParts[0], 1);
            $keys[] = $key;
            $subkeys = $nameParts;
            array_shift($subkeys);
            $data = $transformer ? array_merge($this->data, $transformer($this->data)) : $this->data;
            $value = array_get($data, implode('.', $keys));
            if (isset($parts[1]) && is_array($value)) {
                array_walk($value,
                    function (&$v) use ($parts, $subkeys) {
                    $v = ($subkeys ? array_get($v, implode('.', $subkeys)) : $v) . '.' . $parts[1];
                });
            } elseif (isset($parts[1])) {
                $value = [($subkeys ? array_get($value, implode('.', $subkeys)) : $value) . '.' . $parts[1]];
            }
            return [$key, $value, true];
        }
        return ['', [basename($filename)], false];
    }
}
