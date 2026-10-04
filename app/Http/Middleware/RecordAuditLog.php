<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class RecordAuditLog
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isMethodSafe() && $request->method() !== 'OPTIONS') {
            $actor = $request->user();
            $response = $next($request);
            $actor ??= $request->user();

            if ($actor && Schema::hasTable('audit_logs')) {
                $route = $request->route();
                $routeName = $route?->getName() ?? $request->path();
                $subjectType = null;
                $subjectId = null;

                foreach ($route?->parameters() ?? [] as $key => $parameter) {
                    if ($parameter instanceof Model) {
                        $subjectType = class_basename($parameter);
                        $subjectId = (string) $parameter->getKey();
                        break;
                    }

                    if (is_scalar($parameter)) {
                        $subjectType = Str::studly(Str::singular($key));
                        $subjectId = (string) $parameter;
                        break;
                    }
                }

                AuditLog::create([
                    'user_id' => $actor->getKey(),
                    'action' => Str::of($routeName)
                        ->replace(['.', '_', '-'], ' ')
                        ->squish()
                        ->title()
                        ->limit(255, ''),
                    'route_name' => Str::limit($routeName, 255, ''),
                    'subject_type' => $subjectType,
                    'subject_id' => $subjectId,
                    'status_code' => $response->getStatusCode(),
                    'ip_address' => $request->ip(),
                ]);
            }

            return $response;
        }

        return $next($request);
    }
}