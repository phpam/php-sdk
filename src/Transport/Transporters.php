<?php

namespace Phpam\Sdk\Transport;

enum Transporters: string
{
    case CURL = CurlTransport::class;
    case AsyncCurl = AsyncCurlTransport::class;
}
