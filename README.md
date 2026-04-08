# Calendario PASCOM

Sistema PHP para gestao de calendario paroquial, atividades, usuarios e parquias.

## Requisitos

- PHP 8.1+
- MySQL/MariaDB
- Apache ou XAMPP

## Configuracao

1. Importe o banco em `sql/u596929139_calen.sql`.
2. Ajuste as credenciais em `conexao.php` se necessario.
3. Acesse o projeto em `http://localhost/calender`.

## Acesso

- A pagina inicial fica em `index.php`.
- O login fica em `login.php`.
- O controle de parquias fica em `paroquias.php`.

## Observacoes

- O calendario usa a tabela `atividades` e o schema atual do banco.
- Inscricoes em eventos sao gravadas na tabela `inscricoes`, criada automaticamente quando ausente.
