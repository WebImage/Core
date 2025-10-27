<?php

namespace WebImage\Files;

class FileWatcher
{
    private array $files = [];
    private array $lastModified = [];

    /**
     * Add files to watch
     *
     * @param array $files Array of file paths to watch
     */
    public function addFiles(array $files): void
    {
        foreach ($files as $file) {
            $file = realpath($file);
            if ($file && !in_array($file, $this->files)) {
                $this->files[] = $file;
                $this->lastModified[$file] = file_exists($file) ? filemtime($file) : 0;
            }
        }
    }

    /**
     * Check if any watched files have changed
     *
     * @return array Array of changed files, empty if none changed
     */
    public function checkForChanges(): array
    {
        $changed = [];

        clearstatcache();
        
        foreach ($this->files as $file) {
            if (!file_exists($file)) {
                continue;
            }

            $currentMtime = filemtime($file);

            if ($currentMtime > $this->lastModified[$file]) {
                $changed[] = $file;
                $this->lastModified[$file] = $currentMtime;
            }
        }

        return $changed;
    }

    /**
     * Watch files and execute callback when changes detected
     *
     * @param callable $callback Function to call when changes detected, receives array of changed files
     * @param int $interval Check interval in seconds
     * @param callable|null $onStart Optional callback to run before watching starts
     */
    public function watch(callable $callback, int $interval = 1, ?callable $onStart = null): void
    {
        if ($onStart) {
            $onStart();
        }

        while (true) {
            sleep($interval);

            $changed = $this->checkForChanges();

            if (!empty($changed)) {
                try {
                    $callback($changed);
                } catch (\Exception $e) {
                    // Don't stop watching on error, just report it
                    // The callback should handle error output
                }
            }
        }
    }

    /**
     * Get all watched files
     *
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Clear all watched files
     */
    public function clear(): void
    {
        $this->files = [];
        $this->lastModified = [];
    }
}