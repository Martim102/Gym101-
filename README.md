# Gym101%

Site de fitness com login, progresso, alimentação, amigos e desafios entre utilizadores.

## Estrutura do projeto

```
gym100/
├── database/
│   └── gym100.sql          → esquema da base de dados
├── backend/
│   ├── config/
│   │   ├── db.php          → ligação à base de dados
│   │   └── inicio.php      → sessões e cabeçalhos comuns
│   ├── auth/
│   │   ├── registo.php
│   │   ├── login.php
│   │   ├── logout.php
│   │   └── sessao.php
│   └── api/
│       ├── progresso.php
│       ├── treinos.php
│       ├── alimentacao.php
│       ├── amigos.php
│       └── desafios.php
└── frontend/
    ├── index.html           → login
    ├── registo.html
    ├── css/estilo.css
    ├── js/comum.js
    └── paginas/
        ├── dashboard.html
        ├── progresso.html
        ├── alimentacao.html
        ├── amigos.html
        └── desafios.html
```

## Como correr no XAMPP (passo a passo)

1. **Instala o XAMPP** (se ainda não tiveres): https://www.apachefriends.org
2. Abre a **pasta de instalação do XAMPP** e entra em `htdocs`.
   - Windows normalmente: `C:\xampp\htdocs`
   - Mac: `/Applications/XAMPP/htdocs`
3. Copia a **pasta `gym100` inteira** (com `backend`, `frontend`, `database`) para dentro de `htdocs`.
4. Abre o **painel de controlo do XAMPP** e liga o **Apache** e o **MySQL**.
5. No browser, vai a `http://localhost/phpmyadmin`
6. Cria a base de dados: clica em **"Importar"**, escolhe o ficheiro `database/gym100.sql` e clica em **"Executar"**.
   - Isto já cria a base de dados `gym100` com todas as tabelas.
7. Abre no browser: `http://localhost/gym100/frontend/index.html`
8. Cria a tua conta em **"Cria uma aqui"** e começa a usar o site.

Se mudares a password do MySQL do teu XAMPP (por defeito não tem password), atualiza-a em `backend/config/db.php`.

## Como testar as funcionalidades sociais

Para testares os amigos e os desafios, precisas de pelo menos duas contas:
1. Cria a tua conta normal.
2. Abre uma **janela anónima/privada** do browser e cria uma segunda conta com outro email.
3. Numa das contas, vai a **Amigos → pesquisa pelo nome/email da outra conta → Adicionar**.
4. Na outra conta, aceita o pedido em **Amigos → Pedidos pendentes**.
5. Agora já podes criar um **desafio** e convidar essa conta. Sempre que qualquer uma registar um treino, os pontos do desafio (tipo "Nº de treinos") atualizam automaticamente.

## Colocar o site online de graça

Aqui é preciso ter atenção: o **XAMPP usa PHP + MySQL**, mas a **Vercel só corre sites estáticos ou funções em JavaScript/Node — não corre PHP nem tem MySQL**. Ou seja, não dá para pores este backend diretamente na Vercel. Tens três caminhos possíveis, todos gratuitos:

### Opção A — Hosting gratuito com PHP + MySQL (mais simples, quase nada muda)
Serviços como **InfinityFree** (infinityfree.net) ou **000webhost** dão PHP e MySQL grátis. Fazes upload da pasta `backend` e `frontend` tal como estão, crias a base de dados lá dentro do painel deles (importas o mesmo `gym100.sql`) e ajustas só o `API` no início do `frontend/js/comum.js` para o novo domínio.

### Opção B — Railway ou Render (grátis com limites)
Estes serviços correm PHP + MySQL num contentor. Precisas de ligar o repositório do GitHub e configurar as variáveis de ambiente da base de dados. É um pouco mais técnico mas dá mais controlo.

### Opção C — Separar frontend (Vercel) do backend (outro sítio)
Colocas só a pasta `frontend` na Vercel (isso sim funciona muito bem lá, é só HTML/CSS/JS) e o `backend` num dos serviços da Opção A ou B. Depois mudas o `API` em `comum.js` para apontar para o domínio onde o backend ficou.

**Recomendo a Opção A para já** — é a que menos trabalho dá a partir do que já está feito.

## Publicar o código no GitHub

```bash
cd gym100
git init
git add .
git commit -m "Primeira versão do Gym101%"
git branch -M main
git remote add origin https://github.com/O-TEU-UTILIZADOR/gym100.git
git push -u origin main
```

## Possíveis próximos passos

- Upload de foto de perfil
- Página de definições da conta (mudar nome, password)
- Notificações quando alguém aceita um pedido de amizade
- Gráfico de evolução também para gordura corporal e massa muscular
- Exportar o histórico de progresso em PDF
