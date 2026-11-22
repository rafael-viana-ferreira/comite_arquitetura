<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use src\Http\Response;

require_once __DIR__ . '/../src/Http/Response.php';

class ResponseRotasTest extends \PHPUnit\Framework\TestCase
{
    public function testeRotaInicialRetornaCodigo200()
    {
        $response = new Response();
        $result = $response->init('/', '1.0.0');

        $this->assertEquals(200, $result['status']);
        $this->assertEquals(['status' => 'ok', 'versao' => '1.0.0'], $result['body']);
    }

    public function testeRotaInexistenteRetornaCodigo404()
    {
        $response = new Response();
        $result = $response->init('/rota-teste', '1.0.0');

        $this->assertEquals(404, $result['status']);
        $this->assertEquals(['status' => 'error', 'message' => 'Rota nao encontrada'], $result['body']);
    }

    public function testRotaSaudeRetornaCodigo200()
    {
        $response = new Response();
        $result = $response->init('/saude', '1.0.0');

        $this->assertEquals(200, $result['status']);
        $this->assertEquals(['status' => 'ok', 'versao' => '1.0.0'], $result['body']);
    }
}