<?php

declare(strict_types=1);

/*
 * This file is part of the box project.
 *
 * (c) Kevin Herrera <kevin@herrera.io>
 *     Théo Fidry <theo.fidry@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace KevinGH\Box\Composer;

use KevinGH\Box\Test\FileSystemTestCase;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use function Humbug\get_contents;
use function iterator_to_array;
use function KevinGH\Box\FileSystem\dump_file;
use function KevinGH\Box\FileSystem\mirror;
use function preg_replace;

/**
 * @covers \KevinGH\Box\Composer\ComposerOrchestrator
 */
class ComposerOrchestratorTest extends FileSystemTestCase
{
    private const FIXTURES = __DIR__.'/../../fixtures/composer-dump';
    private const COMPOSER_AUTOLOADER_NAME = 'ComposerAutoloaderInit80c62b20a4a44fb21e8e102ccb92ff05';

    /**
     * @dataProvider provideComposerAutoload
     */
    public function test_it_can_dump_the_autoloader_with_an_empty_composer_json(
        array $whitelist,
        string $prefix,
        string $expectedAutoloadContents
    ): void {
        dump_file('composer.json', '{}');

        ComposerOrchestrator::dumpAutoload($whitelist, $prefix);

        $expectedPaths = [
            'composer.json',
            'vendor/autoload.php',
            'vendor/composer/autoload_classmap.php',
            'vendor/composer/autoload_namespaces.php',
            'vendor/composer/autoload_psr4.php',
            'vendor/composer/autoload_real.php',
            'vendor/composer/autoload_static.php',
            'vendor/composer/ClassLoader.php',
            'vendor/composer/LICENSE',
        ];

        $actualPaths = $this->retrievePaths();

        $this->assertSame($expectedPaths, $actualPaths);

        $this->assertSame(
            $expectedAutoloadContents,
            preg_replace(
                '/ComposerAutoloaderInit[a-z\d]{32}/',
                'ComposerAutoloaderInit80c62b20a4a44fb21e8e102ccb92ff05',
                get_contents($this->tmp.'/vendor/autoload.php')
            )
        );

        $this->assertSame(
            <<<'PHP'
<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
);

PHP
            ,
            preg_replace(
                '/ComposerAutoloaderInit[a-z\d]{32}/',
                'ComposerAutoloaderInit80c62b20a4a44fb21e8e102ccb92ff05',
                get_contents($this->tmp.'/vendor/composer/autoload_psr4.php')
            )
        );
    }

    /**
     * @dataProvider provideComposerAutoload
     */
    public function test_it_cannot_dump_the_autoloader_with_an_invalid_composer_json(
        array $whitelist,
        string $prefix
    ): void {
        mirror(self::FIXTURES.'/dir000', $this->tmp);

        dump_file('composer.json', '');

        try {
            ComposerOrchestrator::dumpAutoload($whitelist, $prefix);

            $this->fail('Expected exception to be thrown.');
        } catch (RuntimeException $exception) {
            $this->assertStringStartsWith(
                'Could not dump the autoload: "./composer.json" does not contain valid JSON',
                $exception->getMessage()
            );
        }
    }

    public function test_it_can_dump_the_autoloader_with_a_composer_json_with_a_dependency(): void
    {
        mirror(self::FIXTURES.'/dir000', $this->tmp);

        ComposerOrchestrator::dumpAutoload([], '');

        $expectedPaths = [
            'composer.json',
            'vendor/autoload.php',
            'vendor/composer/autoload_classmap.php',
            'vendor/composer/autoload_namespaces.php',
            'vendor/composer/autoload_psr4.php',
            'vendor/composer/autoload_real.php',
            'vendor/composer/autoload_static.php',
            'vendor/composer/ClassLoader.php',
            'vendor/composer/LICENSE',
        ];

        $actualPaths = $this->retrievePaths();

        $this->assertSame($expectedPaths, $actualPaths);

        $this->assertSame(
            <<<'PHP'
<?php

// autoload.php @generated by Composer

require_once __DIR__ . '/composer/autoload_real.php';

return ComposerAutoloaderInit80c62b20a4a44fb21e8e102ccb92ff05::getLoader();

PHP
            ,
            preg_replace(
                '/ComposerAutoloaderInit[a-z\d]{32}/',
                'ComposerAutoloaderInit80c62b20a4a44fb21e8e102ccb92ff05',
                get_contents($this->tmp.'/vendor/autoload.php')
            )
        );

        $this->assertSame(
            <<<'PHP'
<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
);

PHP
            ,
            preg_replace(
                '/ComposerAutoloaderInit[a-z\d]{32}/',
                'ComposerAutoloaderInit80c62b20a4a44fb21e8e102ccb92ff05',
                get_contents($this->tmp.'/vendor/composer/autoload_psr4.php')
            )
        );
    }

    /**
     * @dataProvider provideComposerAutoload
     */
    public function test_it_cannot_dump_the_autoloader_if_the_composer_json_file_is_missing(
        array $whitelist,
        string $prefix
    ): void {
        try {
            ComposerOrchestrator::dumpAutoload($whitelist, $prefix);

            $this->fail('Expected exception to be thrown.');
        } catch (RuntimeException $exception) {
            $this->assertStringStartsWith(
                'Could not dump the autoload: Composer could not find a composer.json file in',
                $exception->getMessage()
            );
        }
    }

    /**
     * @dataProvider provideComposerAutoload
     */
    public function test_it_can_dump_the_autoloader_with_a_composer_json_lock_and_installed_with_a_dependency(
        array $whitelist,
        string $prefix,
        string $expectedAutoloadContents
    ): void {
        mirror(self::FIXTURES.'/dir001', $this->tmp);

        ComposerOrchestrator::dumpAutoload($whitelist, $prefix);

        // The fact that there is a dependency in the `composer.json` does not change anything to Composer
        $expectedPaths = [
            'composer.json',
            'composer.lock',
            'vendor/autoload.php',
            'vendor/beberlei/assert/composer.json',
            'vendor/beberlei/assert/lib/Assert/Assert.php',
            'vendor/beberlei/assert/lib/Assert/Assertion.php',
            'vendor/beberlei/assert/lib/Assert/AssertionChain.php',
            'vendor/beberlei/assert/lib/Assert/AssertionFailedException.php',
            'vendor/beberlei/assert/lib/Assert/functions.php',
            'vendor/beberlei/assert/lib/Assert/InvalidArgumentException.php',
            'vendor/beberlei/assert/lib/Assert/LazyAssertion.php',
            'vendor/beberlei/assert/lib/Assert/LazyAssertionException.php',
            'vendor/beberlei/assert/LICENSE',
            'vendor/composer/autoload_classmap.php',
            'vendor/composer/autoload_files.php',
            'vendor/composer/autoload_namespaces.php',
            'vendor/composer/autoload_psr4.php',
            'vendor/composer/autoload_real.php',
            'vendor/composer/autoload_static.php',
            'vendor/composer/ClassLoader.php',
            'vendor/composer/installed.json',
            'vendor/composer/LICENSE',
        ];

        $actualPaths = $this->retrievePaths();

        $this->assertSame($expectedPaths, $actualPaths);

        $this->assertSame(
            $expectedAutoloadContents,
            preg_replace(
                '/ComposerAutoloaderInit[a-z\d]{32}/',
                'ComposerAutoloaderInit80c62b20a4a44fb21e8e102ccb92ff05',
                get_contents($this->tmp.'/vendor/autoload.php')
            )
        );

        $this->assertSame(
            <<<'PHP'
<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Assert\\' => array($vendorDir . '/beberlei/assert/lib/Assert'),
);

PHP
            ,
            preg_replace(
                '/ComposerAutoloaderInit[a-z\d]{32}/',
                'ComposerAutoloaderInit80c62b20a4a44fb21e8e102ccb92ff05',
                get_contents($this->tmp.'/vendor/composer/autoload_psr4.php')
            )
        );
    }

    /**
     * @dataProvider provideComposerAutoload
     */
    public function test_it_can_dump_the_autoloader_with_a_composer_json_and_lock_with_a_dependency(
        array $whitelist,
        string $prefix,
        string $expectedAutoloadContents
    ): void {
        mirror(self::FIXTURES.'/dir002', $this->tmp);

        ComposerOrchestrator::dumpAutoload($whitelist, $prefix);

        // The fact that there is a dependency in the `composer.json` does not change anything to Composer
        $expectedPaths = [
            'composer.json',
            'composer.lock',
            'vendor/autoload.php',
            'vendor/beberlei/assert/composer.json',
            'vendor/beberlei/assert/lib/Assert/Assert.php',
            'vendor/beberlei/assert/lib/Assert/Assertion.php',
            'vendor/beberlei/assert/lib/Assert/AssertionChain.php',
            'vendor/beberlei/assert/lib/Assert/AssertionFailedException.php',
            'vendor/beberlei/assert/lib/Assert/functions.php',
            'vendor/beberlei/assert/lib/Assert/InvalidArgumentException.php',
            'vendor/beberlei/assert/lib/Assert/LazyAssertion.php',
            'vendor/beberlei/assert/lib/Assert/LazyAssertionException.php',
            'vendor/beberlei/assert/LICENSE',
            'vendor/composer/autoload_classmap.php',
            'vendor/composer/autoload_namespaces.php',
            'vendor/composer/autoload_psr4.php',
            'vendor/composer/autoload_real.php',
            'vendor/composer/autoload_static.php',
            'vendor/composer/ClassLoader.php',
            'vendor/composer/LICENSE',
        ];

        $actualPaths = $this->retrievePaths();

        $this->assertSame($expectedPaths, $actualPaths);

        $this->assertSame(
            $expectedAutoloadContents,
            preg_replace(
                '/ComposerAutoloaderInit[a-z\d]{32}/',
                'ComposerAutoloaderInit80c62b20a4a44fb21e8e102ccb92ff05',
                get_contents($this->tmp.'/vendor/autoload.php')
            )
        );

        $this->assertSame(
            <<<'PHP'
<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
);

PHP
            ,
            preg_replace(
                '/ComposerAutoloaderInit[a-z\d]{32}/',
                'ComposerAutoloaderInit80c62b20a4a44fb21e8e102ccb92ff05',
                get_contents($this->tmp.'/vendor/composer/autoload_psr4.php')
            )
        );
    }

    public function provideComposerAutoload()
    {
        $composerAutoloaderName = self::COMPOSER_AUTOLOADER_NAME;

        yield [
            [],
            '',
            <<<PHP
<?php

// autoload.php @generated by Composer

require_once __DIR__ . '/composer/autoload_real.php';

return $composerAutoloaderName::getLoader();

PHP
        ];

        yield [
            ['Acme\Foo'],   // Whitelist is ignored when prefix is empty
            '',
            <<<PHP
<?php

// autoload.php @generated by Composer

require_once __DIR__ . '/composer/autoload_real.php';

return $composerAutoloaderName::getLoader();

PHP
        ];

        yield [
            [],
            '_Box',
            <<<PHP
<?php

// autoload.php @generated by Composer

require_once __DIR__ . '/composer/autoload_real.php';

return $composerAutoloaderName::getLoader();

PHP
        ];

        yield [
            ['Acme\Foo'],
            '_Box',
            <<<PHP
<?php

// autoload.php @generated by Composer

require_once __DIR__ . '/composer/autoload_real.php';

\$loader = $composerAutoloaderName::getLoader();

// Whitelist statements @generated by PHP-Scoper

class_exists('_Box\Acme\Foo');

return \$loader;

PHP
        ];
    }

    /**
     * @return string[]
     */
    private function retrievePaths(): array
    {
        $finder = Finder::create()->files()->in($this->tmp);

        return $this->normalizePaths(iterator_to_array($finder));
    }
}