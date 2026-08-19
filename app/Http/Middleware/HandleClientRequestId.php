<?php

namespace App\Http\Middleware;

use App\Models\OfflineRequest;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleClientRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $clientRequestId = $request->header('X-Client-Request-Id');

        if ($clientRequestId === null) {
            return $next($request);
        }

        $existing = OfflineRequest::where('client_request_id', $clientRequestId)->first();

        if ($existing) {
            if ($existing->redirect_url) {
                return redirect($existing->redirect_url)
                    ->with('success', 'Operasi ini sudah diproses sebelumnya.');
            }

            return response($existing->response_body, $existing->status_code);
        }

        $response = $next($request);

        OfflineRequest::create([
            'client_request_id' => $clientRequestId,
            'user_id' => auth()->id(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'redirect_url' => $response->headers->get('Location'),
            'response_body' => $response->getContent(),
        ]);

        return $response;
    }
}