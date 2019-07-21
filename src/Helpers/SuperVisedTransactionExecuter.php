<?php
/**
 * Created by PhpStorm.
 * User: ehsan
 * Date: 7/21/19
 * Time: 7:22 AM
 */

namespace NovaVoip\Helpers;


class SuperVisedTransactionExecuter
{
    protected $fn;
    /**
     * SuperVisedExecuter constructor.
     * @param $fn
     */
    public function __construct(callable $fn)
    {
        $this->fn = $fn;
    }

    public function __invoke()
    {
        return call_user_func($this->fn);
    }
}