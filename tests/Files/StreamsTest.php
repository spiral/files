<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Files\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Files\FileManager;
use Spiral\Files\FilesInterface;
use Spiral\Files\Streams\StreamWrapper;
use Zend\Diactoros\Stream;

class StreamsTest extends TestCase
{
    public function setUp()
    {
        $files = new FileManager();
        $files->ensureDirectory(FIXTURE_DIRECTORY, FilesInterface::RUNTIME);
    }

    public function tearDown()
    {
        $files = new FileManager();
        $files->deleteDirectory(FIXTURE_DIRECTORY, true);
    }

    public function testGetUri()
    {
        $stream = new Stream('php://memory', 'rw');
        $stream->write("sample text");

        $filename = StreamWrapper::localFilename($stream);

        $this->assertFileExists($filename);
        $this->assertSame(strlen('sample text'), filesize($filename));
        $this->assertSame(md5('sample text'), md5_file($filename));

        $newFilename = FIXTURE_DIRECTORY . 'test.txt';
        copy($filename, $newFilename);

        $this->assertFileExists($newFilename);
        $this->assertSame(strlen('sample text'), filesize($newFilename));
        $this->assertSame(md5('sample text'), md5_file($newFilename));

        //Rewinding
        $this->assertFileExists($newFilename);
        $this->assertSame(strlen('sample text'), filesize($newFilename));
        $this->assertSame(md5('sample text'), md5_file($newFilename));

        $this->assertTrue(StreamWrapper::isWrapped($filename));
        $this->assertFalse(StreamWrapper::isWrapped($newFilename));
    }

    public function testGetResource()
    {
        $stream = new Stream('php://memory', 'rw');
        $stream->write("sample text");

        $resource = StreamWrapper::getResource($stream);

        $this->assertInternalType('resource', $resource);
        $this->assertSame('sample text', stream_get_contents($resource, -1, 0));

        //Rewinding
        $this->assertSame('sample text', stream_get_contents($resource, -1, 0));

        fseek($resource, 7);
        $this->assertSame('text', stream_get_contents($resource, -1));
        $this->assertSame('sample', stream_get_contents($resource, 6, 0));
    }

    public function testWriteIntoStream()
    {
        $stream = new Stream(fopen('php://temp', 'wr+'), 'wr+');
        $file = StreamWrapper::localFilename($stream);

        file_put_contents($file, 'test');

        $this->assertSame('test', file_get_contents($file));

        StreamWrapper::releaseUri($file);
    }
}