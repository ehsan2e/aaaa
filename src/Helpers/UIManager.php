<?php

namespace NovaVoip\Helpers;


class UIManager
{
    protected $activePath = [];

    /**
     * @return string
     */
    public function __toString(): string
    {
        return implode('>', $this->activePath);
    }

    /**
     * @param string $path
     * @param int|null $position
     * @return UIManager
     */
    public function addToActivePath(string $path, int $position = null): UIManager
    {
        if (isset($position)) {
            if ($position < 0) {
                throw new \InvalidArgumentException('Position must be a non-negative integer');
            } elseif ($position === 0) {
                $this->activePath = array_merge([$path => true], $this->activePath);
            } elseif (count($this->activePath) <= $position) {
                return $this->addToActivePath($path);
            } else {
                return array_merge(array_slice($this->activePath, 0, $position), [$path => true], array_slice($this->activePath, $position));
            }
        } else {
            $this->activePath[$path] = true;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getActivePath(): array
    {
        return $this->activePath;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isInActivePath(string $path): bool
    {
        return isset($this->activePath[$path]);
    }

    /**
     * @param string[] ...$paths
     * @return UIManager
     */
    public function setActivePath(string ...$paths): UIManager
    {
        $this->activePath = [];
        foreach ($paths as $path) {
            $this->activePath[$path] = true;
        };
        return $this;
    }
}