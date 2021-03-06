<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticIniteee094314e8b9beebab7de8662cabcb8
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Phpml\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Phpml\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-ai/php-ml/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticIniteee094314e8b9beebab7de8662cabcb8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticIniteee094314e8b9beebab7de8662cabcb8::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
