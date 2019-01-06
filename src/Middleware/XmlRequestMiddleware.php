<?php

namespace Elantha\Api\Middleware;

use \Illuminate\Http\Request;

class XmlRequestMiddleware
{
    public function handle(Request $request, \Closure $next)
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
