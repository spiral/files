<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Files\Stream;

use Psr\Http\Message\StreamInterface;

/**
 * Class contain PSR-7 compatible body.
 */
interface StreamableInterface
{
    /**
     * @return StreamInterface
     */
    public function getStream(): StreamInterface;
}
