<?php

namespace NovaVoip\Helpers;


class CustomUrlHandler{
    protected $handlers = [];

    /**
     * @param string $handler
     * @param string $label
     * @return $this
     */
    public function add(string $handler, string $label)
    {
        $this->handlers[$handler] = $label;
        return $this;
    }

    /**
     * @return array
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }
};