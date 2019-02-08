<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log;

use Traversable;

interface LoggerInterface
{

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function emerg($message, $context = []);

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function alert($message, $context = []);

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function crit($message, $context = []);

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function err($message, $context = []);

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function warn($message, $context = []);

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function notice($message, $context = []);

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function info($message, $context = []);

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function debug($message, $context = []);
}
