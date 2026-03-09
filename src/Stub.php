<?php

declare(strict_types=1);

namespace Squipix\Support;

use InvalidArgumentException;

/**
 * Class     Stub
 *
 * @author   SQUIPIX <info@squipix.com>
 */
class Stub
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The stub path.
     *
     * @var string
     */
    protected $path;

    /**
     * The base path of stub file.
     *
     * @var string|null
     */
    protected static $basePath = null;

    /**
     * The replacements array.
     *
     * @var array
     */
    protected $replaces = [];

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new instance.
     *
     * @param  string  $path
     * @param  array   $replaces
     */
    public function __construct($path, array $replaces = [])
    {
        $this->setPath($path);
        $this->setReplaces($replaces);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get stub path.
     *
     * @return string
     */
    public function getPath(): string
    {
        $path = $this->path;

        if ( ! empty(static::$basePath)) {
            $path = static::$basePath.DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR);
        }

        return $path;
    }

    /**
     * Set stub path.
     *
     * @param  string  $path
     *
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get base path.
     *
     * @return string|null
     */
    public static function getBasePath(): ?string
    {
        return static::$basePath;
    }

    /**
     * Set base path.
     *
     * @param  string  $path
     */
    public static function setBasePath(string $path)
    {
        static::$basePath = $path;
    }

    /**
     * Get replacements.
     *
     * @return array
     */
    public function getReplaces(): array
    {
        return $this->replaces;
    }

    /**
     * Set replacements array.
     *
     * @param  array  $replaces
     *
     * @return $this
     */
    public function setReplaces(array $replaces = []): self
    {
        $this->replaces = $replaces;

        return $this;
    }

    /**
     * Set replacements array.
     *
     * @param  array  $replaces
     *
     * @return $this
     */
    public function replaces(array $replaces = []): self
    {
        return $this->setReplaces($replaces);
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Create new self instance.
     *
     * @param  string  $path
     * @param  array   $replaces
     *
     * @return $this
     */
    public static function create(string $path, array $replaces = []): self
    {
        return new static($path, $replaces);
    }

    /**
     * Create new self instance from full path.
     *
     * @param  string  $path
     * @param  array   $replaces
     *
     * @return $this
     */
    public static function createFromPath(string $path, array $replaces = []): self
    {
        return tap(new static($path, $replaces), function (self $stub) {
            $stub->setBasePath('');
        });
    }

    /**
     * Get stub contents.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->getContents();
    }

    /**
     * Save stub to base path.
     *
     * @param  string  $filename
     *
     * @return bool
     */
    public function save(string $filename): bool
    {
        return $this->saveTo(self::getBasePath(), $filename);
    }

    /**
     * Save stub to specific path.
     *
     * @param  string  $path
     * @param  string  $filename
     *
     * @return bool
     */
    public function saveTo(string $path, string $filename): bool
    {
        return file_put_contents($this->resolveWriteTargetPath($path, $filename), $this->render()) !== false;
    }

    /**
     * Get stub contents.
     *
     * @return string|mixed
     */
    public function getContents()
    {
        $path = $this->getPath();

        $this->ensureReadablePath($path);

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new InvalidArgumentException("Unable to read the stub file [{$path}].");
        }

        foreach ($this->getReplaces() as $search => $replace) {
            $contents = str_replace('$'.strtoupper($search).'$', $replace, $contents);
        }

        return $contents;
    }

    /**
     * Ensure the stub path is readable and scoped to base path (if configured).
     *
     * @param  string  $path
     */
    protected function ensureReadablePath(string $path): void
    {
        if (empty(static::$basePath)) {
            return;
        }

        $basePath = realpath(static::$basePath);
        $stubPath = realpath($path);

        if ($basePath === false || $stubPath === false || ! $this->isWithinBasePath($stubPath, $basePath)) {
            throw new InvalidArgumentException('The stub path must be within the configured base path.');
        }
    }

    /**
     * Resolve and validate the write target path.
     *
     * @param  string  $path
     * @param  string  $filename
     *
     * @return string
     */
    protected function resolveWriteTargetPath(string $path, string $filename): string
    {
        if ($filename === '' || basename($filename) !== $filename || strpos($filename, '..') !== false) {
            throw new InvalidArgumentException('The stub filename is invalid.');
        }

        $directory = realpath($path);

        if ($directory === false || ! is_dir($directory) || ! is_writable($directory)) {
            throw new InvalidArgumentException('The destination path is not writable.');
        }

        if (! empty(static::$basePath)) {
            $basePath = realpath(static::$basePath);

            if ($basePath === false || ! $this->isWithinBasePath($directory, $basePath)) {
                throw new InvalidArgumentException('The destination path must be within the configured base path.');
            }
        }

        return $directory . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Check if a path is within the configured base path.
     *
     * @param  string  $path
     * @param  string  $basePath
     *
     * @return bool
     */
    private function isWithinBasePath(string $path, string $basePath): bool
    {
        $basePath = rtrim($basePath, DIRECTORY_SEPARATOR);

        return $path === $basePath || str_starts_with($path, $basePath . DIRECTORY_SEPARATOR);
    }

    /**
     * Handle magic method __toString.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
