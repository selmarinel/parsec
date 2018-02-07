<?php

namespace Parsec;


use FilesystemIterator;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;

final class Boot
{
    public function __invoke()
    {
        define('PARSEC_ROOT_PATH', __DIR__. DIRECTORY_SEPARATOR);

        $dir = __DIR__ . DIRECTORY_SEPARATOR . 'config';

        $iterator = new FilesystemIterator($dir);
        $files = [];
        foreach ($iterator as $config) {
            /** @var SplFileInfo $config */
            $files[] = $config->getPathname();
        }

        $configs = [];
        foreach ($files as $file) {
            $config = Yaml::parse(file_get_contents($file));
            $configs = array_merge($configs, $config);
        }
        return (object)$configs;
    }
}