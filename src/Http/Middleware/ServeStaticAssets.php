<?php

namespace Apsonex\ServotizerCore\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ServeStaticAssets
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $response = $next($request);

        //        if (isset($_ENV['SERVOTIZER_SSM_PATH']) && $response->getStatusCode() === 404) {
        if ($response->getStatusCode() === 404) {
            //            $requestUri = $request->getRequestUri();
            $requestUri = parse_url($request->url())['path'] ?? null;

            if (!$requestUri) return $response;

            if (!in_array(ltrim($requestUri, '/'), config('servotizer.serve_assets', []))) {
                return $response;
            }

            $asset = null;

            try {
                $asset = (new Client)->get(asset($requestUri));
            } catch (ClientException $e) {
                report($e);
            }

            if ($asset && $asset->getStatusCode() === 200) {
                $headers = collect($asset->getHeaders())
                    ->only(['Content-Length', 'Content-Type'])
                    ->all();

                return response($asset->getBody())->withHeaders($headers);
            }
        }

        return $response;
    }
}
