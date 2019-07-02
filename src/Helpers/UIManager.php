<?php

namespace NovaVoip\Helpers;


use Illuminate\Support\Facades\Auth;

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
     * @param string|array $path
     * @return bool
     */
    public function isInActivePath($path): bool
    {
        if (is_scalar($path)) {
            return isset($this->activePath[$path]);
        }
        foreach ($path as $p) {
            if (isset($this->activePath[$p])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $menuConfig
     * @return array
     */
    public function prepareMenu(array $menuConfig): array
    {
        if(Auth::user()->can('backend-admin')){
            return $menuConfig;
        }

        dd($menuConfig);
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