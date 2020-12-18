<?php
/*
 * This file is part of the OpxCore.
 *
 * Copyright (c) Lozovoy Vyacheslav <opxcore@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpxCore\Log;

use OpxCore\Log\Exceptions\LogFileException;

class LogFile extends AbstractLogger
{
    /**
     * Absolute path and filename to be written.
     *
     * @var string
     */
    protected string $filename;

    /**
     * LogFile constructor.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param mixed $message
     * @param array $context
     *
     * @return  void
     */
    public function log($level, $message, array $context = []): void
    {
        $logMessage = $this->formatMessage($level, $message, $context);

        $this->writeLog($logMessage);
    }

    /**
     * Write message to file.
     *
     * @param string $message
     *
     * @return  void
     */
    protected function writeLog(string $message): void
    {
        if (!isset($this->filename) || trim($this->filename) === '') {
            throw new LogFileException('Log filename not set.');
        }

        if (!file_exists($this->filename)) {
            $path = pathinfo($this->filename, PATHINFO_DIRNAME);

            // Prevent race conditions of mkdir
            if (!is_dir($path) && (!@mkdir($path, 0644, true) || !is_dir($path))) {
                throw new LogFileException("Could not create directory [{$path}]");
            }
        }

        if (@file_put_contents($this->filename, $message, FILE_APPEND) === false) {
            throw new LogFileException("Could not write to file [{$this->filename}]");
        }
    }
}