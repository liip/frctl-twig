<?php

namespace Frctl;

class Loader implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface
{

    protected $aliases = array();

    public function __construct($aliases = array())
    {
        $this->setAliases($aliases);
    }

    public function setAliases($aliases)
    {
        if (!is_array($aliases)) return;

        foreach ($aliases as $handle => $path) {
            if (ctype_digit($handle)) continue;
            $this->aliases[$handle] = $path;
        }
    }

    public function getSource($name)
    {
        return file_get_contents($this->findTemplate($name));
    }

    public function getCacheKey($name)
    {
        return $this->findTemplate($name);
    }

    public function exists($name)
    {
        return !empty($this->aliases[$name]);
    }

    public function isFresh($name, $time)
    {
        return filemtime($this->findTemplate($name)) <= $time;
    }

    protected function findTemplate($name)
    {
        $throw = func_num_args() > 1 ? func_get_arg(1) : true;

        if (!empty($this->aliases[$name])) {
            return $this->aliases[$name];
        }

        if ($throw) {
            $msg = sprintf('Unable to find template "%s".', $name);
            throw new Twig_Error_Loader($msg);
        }

        return false;

    }
}
