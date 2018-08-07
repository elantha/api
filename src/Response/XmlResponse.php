<?php

namespace Grizmar\Api\Response;

use Grizmar\Api\ApiServiceProvider;

class XmlResponse extends BaseResponse
{
    public function __construct()
    {
        $this->header('Content-Type', ApiServiceProvider::CONTENT_TYPE_XML);
    }
}
