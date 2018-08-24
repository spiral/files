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

class DirectoriesTest extends TestCase
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

    public function testEnsureDirectory()
    {
        $files = new Files();
        $directory = FIXTURE_DIRECTORY . 'directory/';

        $this->assertFalse($files->exists($directory));
        $this->assertFalse($files->isDirectory($directory));

        $files->ensureDirectory($directory);

        $this->assertTrue($files->exists($directory));
        $this->assertTrue($files->isDirectory($directory));
    }

    public function testEnsureExistedDirectory()
    {
        $files = new Files();
        $directory = FIXTURE_DIRECTORY . 'directory/';

        $this->assertFalse($files->exists($directory));
        $this->assertFalse($files->isDirectory($directory));

        mkdir($directory);

        $this->assertTrue($files->exists($directory));
        $this->assertTrue($files->isDirectory($directory));

        $files->ensureDirectory($directory);

        $this->assertTrue($files->exists($directory));
        $this->assertTrue($files->isDirectory($directory));
    }

    public function testEnsureNestedDirectory()
    {
        $files = new Files();
        $directory = FIXTURE_DIRECTORY . 'directory/sub/other';

        $this->assertFalse($files->exists($directory));
        $this->assertFalse($files->isDirectory($directory));

        $files->ensureDirectory($directory);

        $this->assertTrue($files->exists($directory));
        $this->assertTrue($files->isDirectory($directory));
    }

    public function testEnsureNestedDirectoryNoRecursivePermissions()
    {
        $files = new Files();
        $directory = FIXTURE_DIRECTORY . 'directory/sub/other';

        $this->assertFalse($files->exists($directory));
        $this->assertFalse($files->isDirectory($directory));

        $files->ensureDirectory($directory, Files::DEFAULT_FILE_MODE, false);

        $this->assertTrue($files->exists($directory));
        $this->assertTrue($files->isDirectory($directory));
    }

    public function testEnsureExistedNestedDirectory()
    {
        $files = new Files();
        $directory = FIXTURE_DIRECTORY . 'directory/sub/other';

        $this->assertFalse($files->exists($directory));
        $this->assertFalse($files->isDirectory($directory));

        mkdir(FIXTURE_DIRECTORY . 'directory');
        mkdir(FIXTURE_DIRECTORY . 'directory/sub');
        mkdir(FIXTURE_DIRECTORY . 'directory/sub/other');

        $this->assertTrue($files->exists($directory));
        $this->assertTrue($files->isDirectory($directory));

        $files->ensureDirectory($directory);

        $this->assertTrue($files->exists($directory));
        $this->assertTrue($files->isDirectory($directory));
    }

    public function testDeleteDirectoryContent()
    {
        $files = new Files();
        $baseDirectory = FIXTURE_DIRECTORY . 'directory/';
        $directory = $baseDirectory . 'sub/other';

        $filenames = [
            $baseDirectory . 'test.file',
            $directory . 'other.file',
            $directory . '.sample'
        ];

        $this->assertFalse($files->exists($baseDirectory));
        $this->assertFalse($files->isDirectory($baseDirectory));

        $this->assertFalse($files->exists($directory));
        $this->assertFalse($files->isDirectory($directory));

        $files->ensureDirectory($directory);

        $this->assertTrue($files->exists($baseDirectory));
        $this->assertTrue($files->isDirectory($baseDirectory));

        $this->assertTrue($files->exists($directory));
        $this->assertTrue($files->isDirectory($directory));

        foreach ($filenames as $filename) {
            $this->assertFalse($files->exists($filename));
            $files->write($filename, 'random-data');
            $this->assertTrue($files->exists($filename));
        }

        $files->deleteDirectory($baseDirectory, true);

        $this->assertTrue($files->exists($baseDirectory));
        $this->assertTrue($files->isDirectory($baseDirectory));

        $this->assertFalse($files->exists($directory));
        $this->assertFalse($files->isDirectory($directory));

        foreach ($filenames as $filename) {
            $this->assertFalse($files->exists($filename));
        }
    }

    public function testDeleteDirectory()
    {
        $files = new Files();
        $baseDirectory = FIXTURE_DIRECTORY . 'directory/';
        $directory = $baseDirectory . 'sub/other';

        $filenames = [
            $baseDirectory . 'test.file',
            $directory . 'other.file',
            $directory . '.sample'
        ];

        $this->assertFalse($files->exists($baseDirectory));
        $this->assertFalse($files->isDirectory($baseDirectory));

        $this->assertFalse($files->exists($directory));
        $this->assertFalse($files->isDirectory($directory));

        $files->ensureDirectory($directory);

        $this->assertTrue($files->exists($baseDirectory));
        $this->assertTrue($files->isDirectory($baseDirectory));

        $this->assertTrue($files->exists($directory));
        $this->assertTrue($files->isDirectory($directory));

        foreach ($filenames as $filename) {
            $this->assertFalse($files->exists($filename));
            $files->write($filename, 'random-data');
            $this->assertTrue($files->exists($filename));
        }

        $files->deleteDirectory($baseDirectory, false);

        $this->assertFalse($files->exists($baseDirectory));
        $this->assertFalse($files->isDirectory($baseDirectory));

        $this->assertFalse($files->exists($directory));
        $this->assertFalse($files->isDirectory($directory));

        foreach ($filenames as $filename) {
            $this->assertFalse($files->exists($filename));
        }
    }

    /**
     * @expectedException \Spiral\Files\Exceptions\FilesException
     */
    public function testDeleteDirectoryButFile()
    {
        $files = new Files();

        $files->write(FIXTURE_DIRECTORY . 'test', 'data');
        $files->deleteDirectory(FIXTURE_DIRECTORY . 'test');
    }

    public function testGetFiles()
    {
        $files = new Files();
        $this->assertNotEmpty($files->getFiles(__DIR__));
    }

    public function testGetFilesPattern()
    {
        $files = new Files();
        $this->assertEmpty($files->getFiles(__DIR__, '*.jpg'));
    }
}