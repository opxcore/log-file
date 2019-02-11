<?php

class LogFile extends \Psr\Log\AbstractLogger
{
    /**
     * Absolute path and filename to be written.
     *
     * @var string
     */
    protected $filename;

    /**
     * LogFile constructor.
     *
     * @param  string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param  mixed  $level
     * @param  string $message
     * @param  array  $context
     *
     * @return  void
     */
    public function log($level, $message, array $context = array()): void
    {
        $logMessage = $this->interpolateMessage($message, $context);

        $this->writeLog($logMessage);
    }

    /**
     * Format message with context and exception stacktrace.
     *
     * @param  string $message
     * @param  array $context
     *
     * @return  string
     *
     * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md#12-message
     */
    protected function interpolateMessage($message, $context): string
    {
        /** @var \Exception $exception */
        $exception = $context['exception'] ?? null;

        if($exception instanceof \Exception) {
            $stackTrace = $exception->getTraceAsString();
        }

        // build a replacement array with braces around the context keys
        $replace = [];

        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        $processed = strtr($message, $replace);

        if(isset($stackTrace)) {
            $processed .= "\n{$stackTrace}";
        }

        return $processed;
    }

    /**
     * Write message to file.
     *
     * @param  string $message
     *
     * @return  void
     */
    protected function writeLog($message): void
    {
        if(!isset($this->filename)) {
            throw new \OpxCore\Log\Exceptions\LogFileException('Log filename not set.');
        }

        if(!file_exists($this->filename)) {
            $path = pathinfo($this->filename, PATHINFO_DIRNAME);

            // Prevent race conditions of mkdir
            if(!is_dir($path) && (!mkdir($path, 0644, true) || !is_dir($path))) {
                throw new \OpxCore\Log\Exceptions\LogFileException("Could not create directory [{$path}]");
            }
        }

        if(file_put_contents($this->filename, $message, FILE_APPEND) === false) {
            throw new \OpxCore\Log\Exceptions\LogFileException("Could not write to file [{$this->filename}]");
        }
    }
}