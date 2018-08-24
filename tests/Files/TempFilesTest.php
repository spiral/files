<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Files\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Files\Files;
use Spiral\Files\FilesInterface;

class TempFilesTest extends TestCase
{
    public function setUp()
    {
        $files = new Files();
        $files->ensureDirectory(FIXTURE_DIRECTORY, FilesInterface::RUNTIME);
    }

    public function tearDown()
    {
        $files = new Files();
        $files->deleteDirectory(FIXTURE_DIRECTORY, true);
    }

    public function testTempFilename()
    {
        $files = new Files();

        $tempFilename = $files->tempFilename();
        $this->assertTrue($files->exists($tempFilename));
        $this->assertSame('', $files->read($tempFilename));

        $files->write($tempFilename, 'sample-data');
        $this->assertSame('sample-data', $files->read($tempFilename));
    }

    public function testTempExtension()
    {
        $files = new Files();

        $tempFilename = $files->tempFilename('txt');
        $this->assertTrue($files->exists($tempFilename));
        $this->assertSame('txt', $files->extension($tempFilename));
        $this->assertSame('', $files->read($tempFilename));

        $files->write($tempFilename, 'sample-data');
        $this->assertSame('sample-data', $files->read($tempFilename));
    }

    public function testTempCustomLocation()
    {
        $files = new Files();

        $tempFilename = $files->tempFilename('txt', FIXTURE_DIRECTORY);
        $this->assertTrue($files->exists($tempFilename));

        $this->assertSame('txt', $files->extension($tempFilename));
        $this->assertSame(
            $files->normalizePath(FIXTURE_DIRECTORY, true),
            $files->normalizePath(dirname($tempFilename), true)
        );

        $this->assertSame('', $files->read($tempFilename));

        $files->write($tempFilename, 'sample-data');
        $this->assertSame('sample-data', $files->read($tempFilename));
    }

    public function testAutoRemovalFilesWithExtensions()
    {
        $files = new Files();

        $tempFilename = $files->tempFilename('txt');
        $this->assertTrue($files->exists($tempFilename));
        $this->assertSame('', $files->read($tempFilename));

        $files->write($tempFilename, 'sample-data');
        $this->assertSame('sample-data', $files->read($tempFilename));

        $files->__destruct();
        $this->assertFileNotExists($tempFilename);
    }
}