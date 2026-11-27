<?php 

namespace App\Http;

class Response {
    public function responseHttp($caminhoUri)
    {
        $caminhoUrlRedirecionamento = $caminhoUri;

        switch ($caminhoUrlRedirecionamento) {
            case '/':
                return['status' => 200, 'versao' => '1.0.0', 'corpo' => 'pagina_inicial'];
                break;

            case '/saude':
                return['status' => 200,  'versao' => '1.0.0', 'corpo' => 'pagina_saude'];
                break;

            default:
                return['status' => 404,  'versao' => '1.0.0', 'corpo' => 'pagina_nao_encontrada'];
                break;
        }
    }
}