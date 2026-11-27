<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Http\Response;

ini_set('display_errors', '0');

$requisicoesUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

try {
    $respostaServidor = new Response();
    $retornoServidor = $respostaServidor->responseHttp($requisicoesUrl);
    http_response_code($retornoServidor['status']);
    echo $retornoServidor['corpo'];
} catch (Throwable $e) {
    http_response_code(500);
    echo "erro detalhado" . $e->getMessage();
    exit;
}