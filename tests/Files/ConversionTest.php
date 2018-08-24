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

class ConversionTest extends TestCase
{
    public function testNormalizeFilePath()
    {
        $files = new Files();

        $this->assertSame('/abc/file.name', $files->normalizePath('/abc\\file.name'));
        $this->assertSame('/abc/file.name', $files->normalizePath('\\abc//file.name'));
    }

    public function testNormalizeDirectoryPath()
    {
        $files = new Files();

        $this->assertSame('/abc/dir/', $files->normalizePath('\\abc/dir', true));
        $this->assertSame('/abc/dir/', $files->normalizePath('\\abc//dir', true));
    }

    public function testRelativePath()
    {
        $files = new Files();

        $this->assertSame(
            'some-filename.txt',
            $files->relativePath('/abc/some-filename.txt', '/abc')
        );

        $this->assertSame(
            '../some-filename.txt',
            $files->relativePath('/abc/../some-filename.txt', '/abc')
        );

        $this->assertSame(
            '../../some-filename.txt',
            $files->relativePath('/abc/../../some-filename.txt', '/abc')
        );

        $this->assertSame(
            './some-filename.txt',
            $files->relativePath('/abc/some-filename.txt', '/abc/..')
        );
    }
}