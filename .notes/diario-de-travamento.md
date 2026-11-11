# Diário de Travamento (Bugs e Bloqueios)

## Primeiras semanas de 06/11/2026 a 27/11/2026 - Erro ao subir o meu ambiente de desenvolvimento 

### 1. O que eu estava tentando fazer?
Estava tentando subir minhas dependências e configurações do Nginx.

### 2. Qual erro exato apareceu?
Não conseguia buildar as minhas imagens.

### 3. O que eu já tentei (e não funcionou)?
Verifiquei se não tinha dependências duplicadas ou libs, mudei ambas de ordem e conferi a sintaxe.

### 4. Como destravei (Solução)?
Bom, basicamente achei um repositório e nele percebi que a cada versão da minha imagem o nome de dependências e libs muda. Com isso, percebi que na versão do PHP 8.4 sempre temos uma lib repetida, sendo elas chamadas primeiramente com um `-dev` na frente e logo depois no script de instalação que o Docker roda dentro do meu `RUN`; e no Nginx percebi que o arquivo `default.conf` estava escrito de forma errada.