<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Запуск таймера
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Обработка запроса
        $response = $next($request);

        // Измерение времени и памяти
        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        // Вычисление времени и памяти
        $executionTime = ($endTime - $startTime) * 1000; // в миллисекундах
        $memoryUsage = ($endMemory - $startMemory) / 1024; // в килобайтах

        // Добавляем заголовки
        $response->headers->set('X-Debug-Time', round($executionTime, 2));
        $response->headers->set('X-Debug-Memory', round($memoryUsage, 2));

        return $response;
    }
}
