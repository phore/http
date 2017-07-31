<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 28.07.17
 * Time: 15:12
 */

namespace Phore\Http;


interface PhoreStreamReceiver
{
    public function onData($data);
}