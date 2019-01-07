<?php

namespace Elantha\Api\Response;

use Spatie\ArrayToXml\ArrayToXml;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class XmlResponse extends BaseResponse
{
    public const CONTENT_TYPE = 'application/xml';

    public function __construct()
    {
        parent::__construct();

        $this->addHeader('Content-Type', static::CONTENT_TYPE);
    }

    public function getAnswer(): HttpResponse
    {
        return \response($this->getContent(), $this->getStatusCode())
            ->withHeaders($this->headers);
    }

    protected function getContent(): string
    {
        return ArrayToXml::convert($this->getMap(), 'xml');
    }
}
