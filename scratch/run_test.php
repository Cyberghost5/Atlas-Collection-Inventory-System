<?php

require __DIR__ . '/../vendor/autoload.php';

$classmap = [];

function build_classmap($dir, &$classmap) {
    if (!is_dir($dir)) return;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $tokens = token_get_all(file_get_contents($file->getPathname()));
            $namespace = '';
            $count = count($tokens);
            for ($i = 0; $i < $count; $i++) {
                if (is_array($tokens[$i]) && $tokens[$i][0] === T_NAMESPACE) {
                    $namespace = '';
                    for ($j = $i + 1; $j < $count; $j++) {
                        if ($tokens[$j] === ';' || $tokens[$j] === '{') break;
                        if (is_array($tokens[$j]) && in_array($tokens[$j][0], [T_STRING, T_NAME_QUALIFIED, T_NS_SEPARATOR])) {
                            $namespace .= $tokens[$j][1];
                        }
                    }
                }
                if (is_array($tokens[$i]) && in_array($tokens[$i][0], [T_CLASS, T_INTERFACE, T_TRAIT, (defined('T_ENUM') ? T_ENUM : -1)])) {
                    for ($j = $i + 1; $j < $count; $j++) {
                        if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                            $className = $tokens[$j][1];
                            $fqcn = trim($namespace . '\\' . $className, '\\');
                            $classmap[$fqcn] = $file->getPathname();
                            break;
                        }
                    }
                }
            }
        }
    }
}

build_classmap(__DIR__ . '/../vendor/phpunit', $classmap);
build_classmap(__DIR__ . '/../vendor/sebastian', $classmap);
build_classmap(__DIR__ . '/../vendor/theseer', $classmap);
build_classmap(__DIR__ . '/../vendor/phar-io', $classmap);

spl_autoload_register(function ($class) use (&$classmap) {
    if (isset($classmap[$class])) {
        require_once $classmap[$class];
        return true;
    }
    return false;
});

use PHPUnit\TextUI\Application;

$app = new Application();
exit($app->run(['phpunit', 'tests/Feature/MilestoneOneTest.php']));
