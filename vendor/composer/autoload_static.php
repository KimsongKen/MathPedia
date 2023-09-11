<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitefeac40753d021ac838e2eb32e94f3b6
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Parsedown' => 
            array (
                0 => __DIR__ . '/..' . '/erusev/parsedown',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitefeac40753d021ac838e2eb32e94f3b6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitefeac40753d021ac838e2eb32e94f3b6::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitefeac40753d021ac838e2eb32e94f3b6::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitefeac40753d021ac838e2eb32e94f3b6::$classMap;

        }, null, ClassLoader::class);
    }
}