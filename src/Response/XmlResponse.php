<?php

namespace Grizmar\Api\Response;

use Grizmar\Api\ApiServiceProvider;

class XmlResponse extends BaseResponse
{
    public function getAnswer()
    {
        return \response($this->getMap(), $this->getStatusCode())
            ->header('Content-Type', ApiServiceProvider::CONTENT_TYPE_XML);
    }
}
