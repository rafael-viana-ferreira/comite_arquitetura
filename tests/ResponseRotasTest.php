<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use src\Http\Response;

require_once __DIR__ . '/../src/Http/Response.php';

class ResponseRotasTest extends \PHPUnit\Framework\TestCase
{
    public function testeRotaInicialRetornaCodigo200()
    {
        $response = new Response();
        $result = $response->responseHttp('/');
        
        $this->assertEquals(200, $result['status']);
        $this->assertSame('pagina_inicial', $result['corpo']);
    }

    public function testeRotaInexistenteRetornaCodigo404()
    {
        $response = new Response();
        $result = $response->responseHttp('/rota-teste');

        $this->assertEquals(404, $result['status']);
        $this->assertSame('pagina_nao_encontrada', $result['corpo']);
    }

    public function testRotaSaudeRetornaCodigo200()
    {
        $response = new Response();
        $result = $response->responseHttp('/saude');

        $this->assertEquals(200, $result['status']);
        $this->assertSame('pagina_saude', $result['corpo']);
    }
}