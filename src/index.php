<?php 

$requisicoesUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($requisicoesUrl) {
    case '/' :
        http_response_code(200);
        echo "testando....";
        break;
    default:
        http_response_code(404);
        echo "<h1> page not fold </h1>";
}