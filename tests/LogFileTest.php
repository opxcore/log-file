<?php
/*
 * This file is part of the OpxCore.
 *
 * Copyright (c) Lozovoy Vyacheslav <opxcore@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpxCore\Tests\Log;

use OpxCore\Log\Exceptions\LogFileException;
use OpxCore\Log\LogFile;
use PHPUnit\Framework\TestCase;

class LogFileTest extends TestCase
{
    protected string $iso8601pattern = '\d\d\d\d-\d\d-\d\dT\d\d:\d\d:\d\d\+\d\d:\d\d';

    protected function getFileName(): string
    {
        $tmpDir = sys_get_temp_dir();

        return tempnam($tmpDir, 'log');
    }

    protected function getFileContent(string $filename): string
    {
        return file_get_contents($filename);
    }

    protected function removeFile(string $filename): void
    {
        unlink($filename);
    }

    public function testLogWriting(): void
    {
        $file = $this->getFileName();
        $logger = new LogFile($file);

        $logger->log('info', 'hello {context}', ['context' => 'world']);

        $content = $this->getFileContent($file);
        $this->removeFile($file);


        self::assertMatchesRegularExpression("/\[{$this->iso8601pattern}\]\sinfo\s\>\shello\sworld/", $content);
    }

    public function testLogEmptyName(): void
    {
        $logger = new LogFile(' ');

        $this->expectException(LogFileException::class);

        $logger->log('info', 'hello {context}', ['context' => 'world']);
    }

    public function testLogPermissionDenied(): void
    {
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'log-test' . DIRECTORY_SEPARATOR . 'log-test';

        $logger = new LogFile($file);

        $this->expectException(LogFileException::class);

        $logger->log('info', 'hello {context}', ['context' => 'world']);
    }

    public function testLogDirPermissionDenied(): void
    {
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'log-test' . DIRECTORY_SEPARATOR . 'log-test' . DIRECTORY_SEPARATOR . 'log-test';

        $logger = new LogFile($file);

        $this->expectException(LogFileException::class);

        $logger->log('info', 'hello {context}', ['context' => 'world']);
    }
}
