<?php
/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace test\functional\Ebay\Assets;

use Psr\Http\Message\ResponseInterface;
use rollun\parser\AbstractLoader;

class RollunLoader extends AbstractLoader
{
    protected function createRating(?ResponseInterface $response, \DateTime $startTime, \DateTime $endTime): int
    {
        return 1;
    }
}
