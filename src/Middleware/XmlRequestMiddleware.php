<?php

namespace Grizmar\Api\Middleware;


class XmlRequestMiddleware
{
    public function handle($request, \Closure $next)
    {
        if (strtolower($request->getContentType()) === 'xml') {

            $content = $request->getContent();

            if ($content) {
                $xml = simplexml_load_string($content, null, LIBXML_NOCDATA);
                $data = json_decode(json_encode($xml), true);

                if ($data) {
                    $request->merge($data);
                }
            }
        }

        return $next($request);
    }
}
