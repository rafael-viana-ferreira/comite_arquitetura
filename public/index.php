<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Http\Response;

ini_set('display_errors', '0');

$requisicoesUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATCH);

try {
    $respostaServidor = new Response();
    $retornoServidor = $respostaServidor->responseHttp();
    http_response_code($retornoServidor['status']);
} catch (Throwable $e) {
    http_response_code(500);
    exit;
}