<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

trait LoggerAwareTrait
{

    /**
     * @var LoggerInterface
     */
    protected $logger = null;

    /**
     * Set logger object
     *
     * @param LoggerInterface $logger
     * @return mixed
     */
    public function setLogger(PsrLoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Get logger object
     *
     * @return null|LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

}
