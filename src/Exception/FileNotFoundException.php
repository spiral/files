<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Files\Exception;

/**
 * When trying to read missing file.
 */
class FileNotFoundException extends FilesException
{
    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        parent::__construct("File '{$filename}' not found");
    }
}
