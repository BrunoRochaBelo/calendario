# ⛪ Sistema Pastoral PASCOM V2

O PASCOM System (Agenda & Pastoral) é uma arquitetura robusta de Gestão Paroquial centralizada num calendário tático de alta performance. Adota os conceitos de _Premium Fluid UI_ (Sensação limpa de Native App) e _Zero-Trust Security_ em seu back-end Vanilla estruturado.

## 🌟 Principais Funcionalidades

- **Multi-tenant Dinâmico:** Multiplas Paróquias operando num mesmo banco de dados sem vazamentos laterais.
- **RBAC Hierárquico Universal:** Mais de 7 instâncias de privilégio, com proteção visual modularizada por perfis, grupos de trabalho restritos, permissões boolianas cruzadas e visão customizada.
- **Agendamento em Alta Escalabilidade:** Ferramenta complexa de Atividades, Micro-catalogações e Sub-Inscrições de pessoas. Perfeito para separar o *Culto* das *Vigílias departamentais*.
- **Arquitetura Anti-Burrice & Anti-Ataques:** Spinners de transação para controle de ansiedade de clique, motor universal reescrito anti-SQL Injection com `mysqli Statements`, e Guardas CSRF para blindagem de ações deletérias via bots ou clicks errôneos de líderes inexperientes.

## ⚙️ Pré-Requisitos Mínimos

Para rodar este monólito de alta eficiência com custo mínimo:
1. **PHP 8.0+**
2. **Servidor HTTP** (Apache, Nginx, ou dev-server interno do php `-S`).
3. **Servidor MySQL / MariaDB**

Nenhum artefato complexo de `npm install` ou Compilação está no caminho da sua produtividade.

## 🚀 Instalação e Deploy (Básico)

1. **Clone do Repositório**: Faça a cópia direta para o seu `htdocs/`, `/var/www/html/` ou `www/` do seu Servidor Local (Ex: Laragon / Xampp).
2. **Base de Dados**: Importe os Schemas estruturais descritos em `schema.txt` na base de sua escolha usando Ferramentas (Como HeidiSQL/DBeaver).
3. **Credenciais**: Abra e edite os cabeçalhos de conexão no motor raiz (Localize o arquivo de `.env` ou o script de configuração global em `functions.php`/`conexao.php`) atrelando seu usuário seguro e dbname do MySQL.
4. **Primeiro Acesso**: Acesse `http://localhost/pascom` na web. Pronto! Use a conta Seed Master para dar vida ao RBAC completo do ecossistema.

---
_Aprofunde-se tecnicamente nos meandros lendo o arquivo [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) e estude as abstrações no [docs/DATA_MODEL.md](docs/DATA_MODEL.md)._
