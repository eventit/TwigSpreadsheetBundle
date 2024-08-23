<?php

declare(strict_types=1);

namespace MewesK\TwigSpreadsheetBundle\Helper;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as BaseFilesystem;
use Traversable;

class Filesystem
{
    private static ?BaseFilesystem $delegate = null;

    /**
     * Creates a directory recursively.
     *
     * @param string|iterable $dirs The directory path
     * @param int $mode The directory mode
     *
     * @throws IOException On any directory creation failure
     */
    public static function mkdir(string|iterable $dirs, int $mode = 0777): void
    {
        self::getDelegate()->mkdir($dirs, $mode);
    }

    /**
     * Checks the existence of files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to check
     *
     * @return bool true if the file exists, false otherwise
     */
    public static function exists(string|iterable $files): bool
    {
        return self::getDelegate()->exists($files);
    }

    /**
     * Removes files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to remove
     *
     * @throws IOException When removal fails
     */
    public static function remove(string|iterable $files): void
    {
        self::getDelegate()->remove($files);
    }

    /**
     * Atomically dumps content into a file.
     *
     * @param string $filename The file to be written to
     * @param string $content The data to write into the file
     *
     * @throws IOException If the file cannot be written to
     */
    public static function dumpFile(string $filename, string $content): void
    {
        self::getDelegate()->dumpFile($filename, $content);
    }

    public static function getDelegate(): BaseFilesystem
    {
        if (!self::$delegate) {
            self::$delegate = new BaseFilesystem();
        }

        return self::$delegate;
    }
}
