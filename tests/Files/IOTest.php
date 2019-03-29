<?php declare(strict_types=1);
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

class IOTest extends TestCase
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

    public function testWrite()
    {
        $files = new Files();

        $filename = FIXTURE_DIRECTORY . '/test.txt';
        $this->assertFalse($files->exists($filename));

        $files->write($filename, 'some-data');
        $this->assertTrue($files->exists($filename));

        $this->assertSame('some-data', file_get_contents($filename));
    }

    public function testWriteAndEnsureDirectory()
    {
        $files = new Files();

        $directory = FIXTURE_DIRECTORY . '/directory/abc/';
        $filename = $directory . 'test.txt';

        $this->assertFalse($files->exists($directory));
        $this->assertFalse($files->exists($filename));

        $this->assertFalse($files->isDirectory($directory));

        $files->write($filename, 'some-data', FilesInterface::READONLY, true);

        $this->assertTrue($files->isDirectory($directory));
        $this->assertTrue($files->exists($filename));
        $this->assertSame('some-data', file_get_contents($filename));
    }

    public function testRead()
    {
        $files = new Files();

        $filename = FIXTURE_DIRECTORY . '/test.txt';
        $this->assertFalse($files->exists($filename));

        $files->write($filename, 'some-data');
        $this->assertTrue($files->exists($filename));

        $this->assertSame('some-data', $files->read($filename));
    }

    /**
     * @expectedException \Spiral\Files\Exception\FileNotFoundException
     * @expectedExceptionMessageRegExp /File '.*test.txt' not found/
     */
    public function testReadMissingFile()
    {
        $files = new Files();

        $filename = FIXTURE_DIRECTORY . '/test.txt';
        $this->assertFalse($files->exists($filename));

        $files->read($filename);
    }

    /**
     * @expectedException \Spiral\Files\Exception\FilesException
     */
    public function testWriteForbidden()
    {
        $files = new Files();
        $files->write(FIXTURE_DIRECTORY, 'data');
    }

    /**
     * @expectedException \Spiral\Files\Exception\FileNotFoundException
     */
    public function testGetPermissionsException()
    {
        $files = new Files();
        $files->getPermissions(FIXTURE_DIRECTORY . '/missing');
    }

    public function testAppend()
    {
        $files = new Files();

        $filename = FIXTURE_DIRECTORY . '/test.txt';
        $this->assertFalse($files->exists($filename));

        $files->append($filename, 'some-data');
        $this->assertTrue($files->exists($filename));

        $this->assertSame('some-data', file_get_contents($filename));

        $files->append($filename, ';other-data');
        $this->assertSame('some-data;other-data', file_get_contents($filename));
    }

    public function testAppendEnsureDirectory()
    {
        $files = new Files();

        $directory = FIXTURE_DIRECTORY . '/directory/abc/';
        $filename = $directory . 'test.txt';

        $this->assertFalse($files->exists($directory));
        $this->assertFalse($files->exists($filename));

        $this->assertFalse($files->isDirectory($directory));

        $files->append($filename, 'some-data', null, true);

        $this->assertTrue($files->isDirectory($directory));
        $this->assertTrue($files->exists($filename));
        $this->assertSame('some-data', file_get_contents($filename));

        $files->append($filename, ';other-data', null, true);
        $this->assertSame('some-data;other-data', file_get_contents($filename));
    }

    public function testTouch()
    {
        $files = new Files();

        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $this->assertFalse($files->exists($filename));
        $files->touch($filename);
        $this->assertTrue($files->exists($filename));
    }

    public function testDelete()
    {
        $files = new Files();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $this->assertFalse($files->exists($filename));

        $files->touch($filename);
        $this->assertTrue($files->exists($filename));

        $files->delete($filename);
        $this->assertFalse($files->exists($filename));
    }

    public function testDeleteMissingFile()
    {
        $files = new Files();
        $filename = FIXTURE_DIRECTORY . '/test.txt';

        $this->assertFalse($files->exists($filename));
        $files->delete($filename);
    }

    public function testCopy()
    {
        $files = new Files();
        $filename = FIXTURE_DIRECTORY . '/test.txt';
        $destination = FIXTURE_DIRECTORY . '/new.txt';

        $this->assertFalse($files->exists($filename));
        $files->write($filename, 'some-data');

        $this->assertTrue($files->exists($filename));
        $this->assertSame('some-data', file_get_contents($filename));

        $this->assertFalse($files->exists($destination));

        $this->assertTrue($files->copy($filename, $destination));
        $this->assertTrue($files->exists($destination));
        $this->assertTrue($files->exists($filename));

        $this->assertSame(file_get_contents($filename), file_get_contents($destination));
    }

    /**
     * @expectedException \Spiral\Files\Exception\FileNotFoundException
     * @expectedExceptionMessageRegExp /File '.*test.txt' not found/
     */
    public function testCopyMissingFile()
    {
        $files = new Files();
        $filename = FIXTURE_DIRECTORY . '/test.txt';
        $destination = FIXTURE_DIRECTORY . '/new.txt';

        $this->assertFalse($files->exists($filename));
        $files->copy($filename, $destination);
    }

    public function testMove()
    {
        $files = new Files();
        $filename = FIXTURE_DIRECTORY . '/test.txt';
        $destination = FIXTURE_DIRECTORY . '/new.txt';

        $this->assertFalse($files->exists($filename));
        $files->write($filename, 'some-data');

        $this->assertTrue($files->exists($filename));
        $this->assertSame('some-data', file_get_contents($filename));

        $this->assertFalse($files->exists($destination));

        $this->assertTrue($files->move($filename, $destination));
        $this->assertTrue($files->exists($destination));
        $this->assertFalse($files->exists($filename));

        $this->assertSame('some-data', file_get_contents($destination));
    }

    /**
     * @expectedException \Spiral\Files\Exception\FileNotFoundException
     * @expectedExceptionMessageRegExp /File '.*test.txt' not found/
     */
    public function testMoveMissingFile()
    {
        $files = new Files();
        $filename = FIXTURE_DIRECTORY . '/test.txt';
        $destination = FIXTURE_DIRECTORY . '/new.txt';

        $this->assertFalse($files->exists($filename));
        $files->move($filename, $destination);
    }
}