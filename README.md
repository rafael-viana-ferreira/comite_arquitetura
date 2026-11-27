# Plataforma de Ingressos

Projeto de uma plataforma de venda e reserva de ingressos para eventos (com cenários de pico de acesso, como abertura de vendas com dezenas de milhares de acessos simultâneos). Na Fase 1 (módulos 01 a 13), o sistema é construído **na mão** em PHP 8.4 e Nginx, sem framework web nem ORM, seguindo TDD desde o início.

---

## Regras da trilha

* **Sem IA:** Nada de chat, assistente ou autocomplete com IA em código, testes, ADRs, depuração ou commits. Travou? Tente sozinho por até 1 hora, registre o que tentou e leve ao mentor.
* **Primeiro à mão, depois a ferramenta:** Roteador, front controller, container, migrations, sessão e worker de fila entram escritos à mão na Fase 1. Ferramentas de desenvolvimento como o PHPUnit ficam em `require-dev`, fora do código que atende o comprador.
* **Diários:**
  * **Diário de dores:** registra o que for trabalhoso ou repetitivo para justificar ferramentas da Fase 2.
  * **Diário de travamentos:** registra bloqueios e tentativas feitas antes de pedir ajuda.
* **ADRs (`docs/adr/`):** decisões relevantes ficam documentadas com contexto (com números), opções consideradas, decisão e consequências — começando pelo `ADR 0001` sobre o próprio registro.
* **TDD (Red-Green-Refactor):** todo código de produção nasce de um teste que falha antes pelo motivo certo (`test:` → `feat:`/`fix:` → `refactor:`), com um teste por vez.
* **Git e Conventional Commits:** branches saem da `main` (ex.: `feat/rota-de-saude`) e entram via PR/MR. Commits pequenos, uma intenção por commit, explicando o *porquê* no corpo, usando apenas `test`, `feat`, `fix`, `refactor`, `docs`, `chore` e `ci`. `.gitignore` ignora `vendor/`, `.env` e arquivos locais/de editor desde o primeiro commit — segredos nunca entram no repositório.

---

## Como subir

```bash
docker compose up -d --build
