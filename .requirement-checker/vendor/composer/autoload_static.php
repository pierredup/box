<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc78ac325e1ae4545ddd78e13112286da
{
    public static $prefixLengthsPsr4 = array (
        '_' => 
        array (
            '_HumbugBox5aeb92ac2e46b\\KevinGH\\RequirementChecker\\' => 51,
            '_HumbugBox5aeb92ac2e46b\\Composer\\Semver\\' => 40,
        ),
    );

    public static $prefixDirsPsr4 = array (
        '_HumbugBox5aeb92ac2e46b\\KevinGH\\RequirementChecker\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        '_HumbugBox5aeb92ac2e46b\\Composer\\Semver\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/semver/src',
        ),
    );

    public static $classMap = array (
        '_HumbugBox5aeb92ac2e46b\\Composer\\Semver\\Comparator' => __DIR__ . '/..' . '/composer/semver/src/Comparator.php',
        '_HumbugBox5aeb92ac2e46b\\Composer\\Semver\\Constraint\\AbstractConstraint' => __DIR__ . '/..' . '/composer/semver/src/Constraint/AbstractConstraint.php',
        '_HumbugBox5aeb92ac2e46b\\Composer\\Semver\\Constraint\\Constraint' => __DIR__ . '/..' . '/composer/semver/src/Constraint/Constraint.php',
        '_HumbugBox5aeb92ac2e46b\\Composer\\Semver\\Constraint\\ConstraintInterface' => __DIR__ . '/..' . '/composer/semver/src/Constraint/ConstraintInterface.php',
        '_HumbugBox5aeb92ac2e46b\\Composer\\Semver\\Constraint\\EmptyConstraint' => __DIR__ . '/..' . '/composer/semver/src/Constraint/EmptyConstraint.php',
        '_HumbugBox5aeb92ac2e46b\\Composer\\Semver\\Constraint\\MultiConstraint' => __DIR__ . '/..' . '/composer/semver/src/Constraint/MultiConstraint.php',
        '_HumbugBox5aeb92ac2e46b\\Composer\\Semver\\Semver' => __DIR__ . '/..' . '/composer/semver/src/Semver.php',
        '_HumbugBox5aeb92ac2e46b\\Composer\\Semver\\VersionParser' => __DIR__ . '/..' . '/composer/semver/src/VersionParser.php',
        '_HumbugBox5aeb92ac2e46b\\KevinGH\\RequirementChecker\\Checker' => __DIR__ . '/../..' . '/src/Checker.php',
        '_HumbugBox5aeb92ac2e46b\\KevinGH\\RequirementChecker\\IO' => __DIR__ . '/../..' . '/src/IO.php',
        '_HumbugBox5aeb92ac2e46b\\KevinGH\\RequirementChecker\\Printer' => __DIR__ . '/../..' . '/src/Printer.php',
        '_HumbugBox5aeb92ac2e46b\\KevinGH\\RequirementChecker\\Requirement' => __DIR__ . '/../..' . '/src/Requirement.php',
        '_HumbugBox5aeb92ac2e46b\\KevinGH\\RequirementChecker\\RequirementCollection' => __DIR__ . '/../..' . '/src/RequirementCollection.php',
        '_HumbugBox5aeb92ac2e46b\\KevinGH\\RequirementChecker\\Terminal' => __DIR__ . '/../..' . '/src/Terminal.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc78ac325e1ae4545ddd78e13112286da::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc78ac325e1ae4545ddd78e13112286da::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc78ac325e1ae4545ddd78e13112286da::$classMap;

        }, null, ClassLoader::class);
    }
}