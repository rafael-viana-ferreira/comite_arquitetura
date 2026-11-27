## O que acontece quando o usuário dá enter

Basicamente o navegador não consegue identificar por conta própria os domínios como `https://skoob.com/`. Ele não sabe chegar nesse domínio, pois o que ele entende é de IPs e, pra isso, o nosso navegador passa por 4 fases: **DNS**, **TCP**, **TLS** (caso usemos o HTTPS, que é uma forma de comunicação segura que junta o HTTP mais o TLS para termos um protocolo de comunicação segura) e o **HTTP**, que é o protocolo que usamos para a comunicação.

---

## DNS

Utilizando o `dig` conseguimos analisar com cautela a estrutura abaixo de um DNS, no qual eu rodei o comando no domínio `skoob.com.br`:

```text
;; MSG SIZE  rcvd: 54
; <<>> DiG 9.20.24-1ubuntu0.5-Ubuntu <<>> skoob.com.br
;; global options: +cmd
;; Got answer:
;; ->>HEADER<<- opcode: QUERY, status: NOERROR, id: 19411
;; flags: qr rd ra; QUERY: 1, ANSWER: 2, AUTHORITY: 0, ADDITIONAL: 1

;; OPT PSEUDOSECTION:
; EDNS: version: 0, flags:; udp: 65494
;; QUESTION SECTION:
;skoob.com.br.			IN	A

;; ANSWER SECTION:
skoob.com.br.		300	IN	A	104.20.47.101
skoob.com.br.		300	IN	A	172.66.144.146

;; Query time: 23 msec
;; SERVER: 127.0.0.53#53(127.0.0.53) (UDP)
;; WHEN: Thu Nov 26 11:34:26 -04 2026
;; MSG SIZE  rcvd: 73
```

Vamos explicar o que o nosso cabeçalho retornou:

* **`status: NOERROR`** -> aqui significa que o nome, no caso `skoob.com.br`, que é o domínio, existe.
* **`skoob.com.br. 300 IN A 104.20.47.101` e `172.66.144.146`** -> os dois IPv4 do domínio que consultamos.
* **`300`** -> esse número aqui é o tempo (em segundos) que cada resposta pode ficar no cache; geralmente ele fica na mesma linha do nome de domínio e do IPv4.
* **`SERVER: 127.0.0.53#53(127.0.0.53) (UDP)`** -> é o server que respondeu, no caso o cache DNS local da minha máquina.
* **`Query time: 23 msec`** -> esse é o tempo de resposta.

---

## TLS

A conexão TLS funciona em conjunto com o TCP, e o TCP roda na porta `443`, onde negocia com o TLS:

```text
Certificate chain
 0 s:CN=skoob.com
   i:C=US, O=Google Trust Services, CN=WE1
   a:PKEY: EC, (prime256v1); sigalg: ecdsa-with-SHA256
   v:NotBefore: Nov  8 21:29:12 2026 GMT; NotAfter: Feb  6 22:28:56 2027 GMT
 1 s:C=US, O=Google Trust Services, CN=WE1
   i:C=US, O=Google Trust Services LLC, CN=GTS Root R4
   a:PKEY: EC, (prime256v1); sigalg: ecdsa-with-SHA384
   v:NotBefore: Dec 13 09:00:00 2023 GMT; NotAfter: Feb 20 14:00:00 2029 GMT
 2 s:C=US, O=Google Trust Services LLC, CN=GTS Root R4
   i:C=BE, O=GlobalSign nv-sa, OU=Root CA, CN=GlobalSign Root CA
   a:PKEY: EC, (secp384r1); sigalg: sha256WithRSAEncryption
   v:NotBefore: Nov 15 03:43:21 2023 GMT; NotAfter: Jan 28 00:00:42 2028 GMT
```

* **`0`** -> mostra o servidor no qual foi rodado o comando, no caso o servidor dono desse certificado (`skoob.com`).
* **`1`** -> nesse 1 mostra informações e assinaturas da intermediária (`WE1`); nesse caso esse certificado é assinado pela raiz `GTS Root R4`.
* **`2` (Certificado Cruzado)** -> `GTS Root R4` assinada pela `GlobalSign Root CA` (garante compatibilidade com dispositivos muito antigos que ainda não têm a raiz do Google instalada).

**O que é a raiz:** podemos dizer que é um certificado de mais alto nível, como GlobalSign, DigiCert, Sectigo ou Google Trust Services.

Bom, o que podemos dizer dessa cadeia é que ela tem 3 níveis: temos o servidor que possui o certificado, que é o número `0`, e o `1`, que é a intermediária assinada por uma raiz voltada pra aparelhos mais novos que já possuem esse certificado direto no sistema. Ou seja, o nosso servidor não assina nada na hora; os certificados são assinados antes e o que acontece é que eles ficam salvos no servidor e, quando consultados, são entregues.

E na conexão o meu computador recebe essa pilha de certificados e confere a assinatura; se estiver ok, aí sim a conexão é marcada como segura. Se não, recebemos o alerta de conexão não segura. O cruzamento é porque alguns aparelhos antigos que não recebem mais atualização do sistema operacional meio que ficam travados nos certificados anteriores à atualização, e é por isso que temos o cruzamento. O Skoob (via Cloudflare), pensando nesses caras, resolveu utilizar do cruzamento para ter dois caminhos de validação: um direto para aparelhos mais novos e uma ponte para uma raiz mais antiga para aparelhos antigos, e assim não ter conexão bloqueada em máquinas ultrapassadas.

---

## HTTP

Basicamente esse carinha é o nosso protocolo de comunicação, a camada de dentro da cebola: o cara que faz o pedido de fato depois que o aparelho passou pelo DNS e fechou o túnel TCP + TLS que rodam juntos na porta `443`.

```text
* IPv4: 4.228.31.150
*   Trying 4.228.31.150:443...
* ALPN: curl offers h2,http/1.1
* SSL connection using TLSv1.3 / TLS_AES_128_GCM_SHA256 / X25519
* ALPN: server accepted h2
*  SSL certificate verify ok.
> GET / HTTP/2
> Host: github.com
> User-Agent: curl/8.5.0
> Accept: */*
>
< HTTP/2 200
< content-type: text/html; charset=utf-8
< cache-control: max-age=0, private, must-revalidate
< strict-transport-security: max-age=31536000; includeSubdomains; preload
< server: github.com
```

A versão do HTTP é escolhida durante o TLS, pelo ALPN: o `curl` ofereceu `h2` e `http/1.1`, e o servidor escolheu `h2`, ou seja, HTTP/2. O status foi `200`, que significa sucesso. Três cabeçalhos da resposta:

* **`content-type: text/html; charset=utf-8`**: o corpo é uma página HTML em UTF-8.
* **`cache-control: max-age=0, private, must-revalidate`**: a página não pode ficar em cache compartilhado e precisa ser confirmada com o servidor antes de ser reaproveitada, porque ela muda conforme quem está logado.
* **`strict-transport-security: max-age=31536000`**: durante um ano, o navegador só deve acessar o GitHub por HTTPS.

---

## No meu ambiente local

Aqui não temos um DNS público e muito menos um TLS, ou seja, não temos um certificado pois nosso servidor é local:

```text
* Host localhost:80 was resolved.
* IPv6: ::1
* IPv4: 127.0.0.1
*   Trying [::1]:80...
* Established connection to localhost (::1 port 80) from ::1 port 36230 
* using HTTP/1.x
> GET /saude HTTP/1.1
> Host: localhost
> User-Agent: curl/8.18.0
> Accept: */*
> 
* Request completely sent off
< HTTP/1.1 200 OK
< Server: nginx
< Date: Fri, 27 Nov 2026 00:25:21 GMT
< Content-Type: text/html; charset=UTF-8
< Transfer-Encoding: chunked
< Connection: keep-alive
< 
* Connection #0 to host localhost:80 left intact
```

Bom, o que podemos ver aqui: o `localhost` resolveu tanto IPv6 (`::1`, que foi o usado na conexão) quanto IPv4 (`127.0.0.1`). A versão ainda está em `HTTP/1.1` pois não houve a negociação entre as camadas TLS e HTTP (ALPN), por isso foi utilizado o 1.1.

Como estou utilizando o Docker com Nginx, o meu server está escutando na porta `80` e recebendo a rota `/saude`. Ele compara com as minhas regras vendo que ela não é um arquivo estático existente e, como configurei no meu arquivo `default.conf`, ele vai jogar tudo para o meu arquivo `index.php`, onde está o tratamento. Esse comportamento chamamos de **Front Controller**: mesmo se digitarem outro arquivo `.php` direto na URL, o meu server sempre vai caçar o `index.php` (ou devolver um `404`).

Quando eu digito uma rota válida e não um arquivo, o meu tratamento cai nos blocos `location` que vão processar essa requisição dentro do `index.php`, onde ele vai renderizar ou retornar a resposta proposta por aquela rota — ou, se no `index.php` essa rota não existir, ele simplesmente vai retornar um `404`. Ou seja, se eu tentar consultar qualquer arquivo `.php` direto pela URL, meu sistema vai travar o acesso; caso seja o `index.php`, ele centraliza tudo para permitir apenas rotas como `/alguma-coisa` a serem processadas.

No nosso caso, além de estarmos em `HTTP/1.1` por estarmos localmente sem certificado, sem DNS público e sem TLS (rodando só TCP + HTTP direto), tudo fica restrito ao endereço de loopback (`127.0.0.1` / `::1`) da minha própria máquina.
