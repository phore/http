<?php

namespace Phore\Http;

function request(string $url) : PhoreHttp {
    return new PhoreHttp($url);
}
