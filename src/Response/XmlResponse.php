<?php

namespace Grizmar\Api\Response;


class XmlResponse extends BaseResponse
{
    public const CONTENT_TYPE = 'application/xml';

    public function __construct()
    {
        parent::__construct();

        $this->addHeader('Content-Type', static::CONTENT_TYPE);
    }
}
