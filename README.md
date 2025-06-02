# catolog-movie

Todo o desenvolvimento com issues e branchs criadas podem ser visualizadas aqui : 

https://github.com/users/VicMoura/projects/4/views/1

# Como rodar o projeto localmente com Docker

## 1. Pré-requisitos

Ter o Docker e Docker Compose instalados na sua máquina.

## 2. Sobre a configuração Docker

No seu projeto, o ambiente é composto por 4 serviços:

| Serviço    | O que faz                     | Porta exposta localmente |
|------------|------------------------------|-------------------------|
| backend    | Rodar a aplicação Laravel PHP| 8000                    |
| frontend   | Rodar a aplicação Vue.js      | 5173                    |
| mysql      | Banco de dados MySQL          | 3306                    |
| phpmyadmin | Interface web para gerenciar o MySQL | 8080             |

## 3. Passos para subir o ambiente

1. Clone o repositório:

```bash
git clone <url-do-repositorio>
cd <nome-da-pasta>
```

2. Suba os containers:

```bash
docker-compose up -d
```

Isso irá:

- Construir as imagens Docker para backend e frontend, se necessário  
- Subir os containers  
- Conectar tudo na rede `app-network`

3. Acesse os serviços no navegador:

- Backend Laravel API: [http://localhost:8000](http://localhost:8000)  
- Frontend Vue.js: [http://localhost:5173](http://localhost:5173)  
- PHPMyAdmin para gerenciar o banco: [http://localhost:8080](http://localhost:8080)  

**Login no PHPMyAdmin:**  
- Host: `mysql`  
- Usuário: `root`  
- Senha: `root`

## 4. Como os containers funcionam

### Backend (Laravel):

- Imagem construída a partir do Dockerfile dentro da pasta `/backend`.  
- Código fonte montado via volume para dentro do container (`./backend:/var/www`).  
- Porta 8000 do container mapeada para 8000 na máquina local.  
- Comando inicial via script `entrypoint.sh` que prepara o ambiente e inicia o servidor PHP-FPM.  
- Xdebug configurado para facilitar o debug local.

### Frontend (Vue.js):

- Construído a partir do Dockerfile dentro da pasta `/frontend`.  
- Código fonte montado via volume (`./frontend:/app`).  
- Porta 5173 do container mapeada para 5173 localmente.  
- Comando padrão roda o servidor de desenvolvimento Vue (`npm run dev`).

### MySQL:

- Utiliza a imagem oficial `mysql:8.0`.  
- Banco `filmes` criado automaticamente.  
- Porta 3306 exposta para acesso local.  
- Dados persistidos no volume `mysql_data` para manter mesmo se o container parar.

### PHPMyAdmin:

- Interface web para gerenciar o banco.  
- Conecta-se ao MySQL via hostname `mysql`.

## 5. Como rodar os tests units backend

# Como Rodar os Testes do Backend no Docker

Docker do backend Laravel está rodando com o nome `laravel_backend`, siga os passos abaixo para executar os testes:

### 1. Acesse o container Docker

Abra seu terminal e execute o comando para acessar o shell do container:

```bash
docker exec -it laravel_backend bash
``` 
Dentro do container, rode o comando abaixo para executar os testes automatizados (usando PHPUnit):

```bash
php artisan test
``` 

# Como Obter a Chave da API do TMDB

Para utilizar a API do TMDB (The Movie Database) no seu projeto, é necessário ter uma chave de API válida. Esta chave permite que você faça requisições e obtenha dados de filmes, séries e outras informações da base do TMDB.

---

## Passos para obter sua chave da API TMDB

1. Acesse o site oficial do TMDB:  
   [https://www.themoviedb.org/](https://www.themoviedb.org/)

2. Crie uma conta gratuita, clicando em **Sign Up** (Cadastrar).

3. Após confirmar seu e-mail e fazer login, acesse a seção de configurações da sua conta (Settings).

4. Navegue até a aba **API**.

5. Solicite uma chave de API preenchendo as informações necessárias (geralmente sobre o uso da API).

6. Após a aprovação, você terá acesso à sua chave de API (API Key).

---

## Como usar a chave da API no projeto

No arquivo `.env` do seu projeto Laravel, existe a variável:


```bash
TMDB_API_KEY=3953f9bb3cb8a1d5cf169689d53a1e40
``` 

Esta chave já está configurada para uso imediato.

### Caso queira usar outra chave

1. Substitua o valor da variável `TMDB_API_KEY` no arquivo `.env` pela nova chave obtida no site do TMDB.

2. Salve o arquivo.

3. Caso o projeto já esteja rodando, reinicie o servidor Laravel para que a nova chave seja carregada.

---

Pronto! Agora seu backend estará configurado para utilizar a API do TMDB com a chave correta.

# Estrutura dos Controllers do Backend Laravel

## 1. FavoritesMoviesController

**Descrição:**  
Gerencia operações relacionadas aos filmes favoritos dos usuários.

**Dependências:**  
- `FavoriteMovieService` (serviço que implementa a lógica de negócio para favoritos).

**Principais métodos:**

- `create(CreateFavoritesMoviesRequest $request): JsonResponse`  
  Adiciona um filme à lista de favoritos do usuário autenticado.  
  - Recebe o `tmdb_id` do filme via requisição.  
  - Retorna a lista atualizada de favoritos com mensagem de sucesso.

- `list(ListFavoritesMoviesRequest $request): JsonResponse`  
  Lista os filmes favoritos do usuário, podendo filtrar por gênero.  
  - Recebe opcionalmente um `genre_id` para filtro.  
  - Retorna uma coleção dos filmes favoritos.

- `delete(Request $request, int $tmdbId): JsonResponse`  
  Remove um filme da lista de favoritos do usuário.  
  - Recebe o `tmdbId` do filme a ser removido.  
  - Retorna confirmação da remoção.

---

## 2. GenreController

**Descrição:**  
Responsável por fornecer a lista de gêneros disponíveis para os filmes.

**Principais métodos:**

- `index()`  
  Busca todos os gêneros cadastrados na base de dados.  
  - Retorna uma resposta JSON com os campos: `id`, `name`, e `tmdb_genre_id`.

---

## 3. TmdbController

**Descrição:**  
Controla a comunicação com a API do TMDB para buscar e detalhar filmes.

**Dependências:**  
- `TmdbService` (serviço que encapsula chamadas à API TMDB).

**Principais métodos:**

- `search(Request $request)`  
  Busca filmes por uma query de pesquisa.  
  - Valida que a query tenha ao menos 2 caracteres.  
  - Retorna uma lista de filmes formatados com:  
    - `tmdb_id`  
    - `title`  
    - `overview`  
    - `poster_path`  
    - `release_date`

- `detail(int $tmdbId)`  
  Retorna detalhes completos de um filme a partir do ID TMDB.

**Métodos auxiliares:**

- `transformMovie(array $movie): array`  
  Formata os dados do filme para o padrão utilizado na API.

---

## 4. UserController

**Descrição:**  
Gerencia autenticação, registro e dados do usuário.

**Dependências:**  
- `UserService` (serviço responsável pela lógica de usuários e autenticação).

**Principais métodos:**

- `create(RegisterUserRequest $request): JsonResponse`  
  Registra um novo usuário com dados validados.  
  - Retorna dados do usuário criado e status 201.

- `login(LoginUserRequest $request): JsonResponse`  
  Realiza login com credenciais validadas.  
  - Retorna token ou dados de sessão.

- `detail(Request $request): JsonResponse`  
  Retorna os dados do usuário autenticado atual.

- `logout(Request $request): JsonResponse`  
  Realiza logout, revogando o token atual.

# Estrutura dos Form Requests no Backend Laravel

---

## 1. CreateFavoritesMoviesRequest

**Descrição:**  
Valida a requisição para criar um favorito, ou seja, adicionar um filme à lista de favoritos do usuário.

**Regras de validação:**  
- `tmdb_id`:  
  - Obrigatório (`required`)  
  - Deve ser inteiro (`integer`)  
  - Validação customizada que verifica se o filme já está nos favoritos do usuário, evitando duplicação.

**Mensagens personalizadas:**  
- `tmdb_id.required`: "O ID do filme (TMDB) é obrigatório."  
- `tmdb_id.integer`: "O ID do filme (TMDB) deve ser um número inteiro."  
- Caso o filme já esteja favoritado, retorna: "Esse filme já está nos seus favoritos."

---

## 2. ListFavoritesMoviesRequest

**Descrição:**  
Valida a requisição para listar os filmes favoritos, com filtro opcional por gênero.

**Regras de validação:**  
- `genre_id`:  
  - Opcional (`nullable`)  
  - Deve ser inteiro (`integer`)  
  - Deve existir na tabela `genres` (`exists:genres,id`).

**Mensagens personalizadas:**  
- `genre_id.integer`: "O ID do gênero deve ser um número inteiro."  
- `genre_id.exists`: "O gênero informado não foi encontrado."

---

## 3. LoginUserRequest

**Descrição:**  
Valida os dados enviados para o login do usuário.

**Regras de validação:**  
- `email`:  
  - Obrigatório (`required`)  
  - Deve ser email válido (`email`)  
  - Deve existir na tabela `users` (`exists:users,email`).  
- `password`:  
  - Obrigatório (`required`)  
  - Deve ser texto (`string`).

**Mensagens personalizadas:**  
- `email.required`: "O email é obrigatório."  
- `email.email`: "Informe um email válido."  
- `email.exists`: "Este email não está cadastrado."  
- `password.required`: "A senha é obrigatória."  
- `password.string`: "A senha deve ser um texto."

---

## 4. RegisterUserRequest

**Descrição:**  
Valida os dados enviados para o registro de um novo usuário.

**Regras de validação:**  
- `name`:  
  - Obrigatório (`required`)  
  - Deve ser texto (`string`)  
  - Máximo 255 caracteres (`max:255`).  
- `email`:  
  - Obrigatório (`required`)  
  - Deve ser email válido (`email`)  
  - Deve ser único na tabela `users` (`unique:users,email`).  
- `password`:  
  - Obrigatório (`required`)  
  - Deve ser texto (`string`)  
  - Mínimo 6 caracteres (`min:6`)  
  - Deve ser confirmado (campo `password_confirmation`) (`confirmed`).

**Mensagens personalizadas:**  
- `name.required`: "O nome é obrigatório."  
- `name.string`: "O nome deve ser um texto."  
- `name.max`: "O nome pode ter no máximo 255 caracteres."  
- `email.required`: "O email é obrigatório."  
- `email.email`: "Informe um email válido."  
- `email.unique`: "Este email já está em uso."  
- `password.required`: "A senha é obrigatória."  
- `password.string`: "A senha deve ser um texto."  
- `password.min`: "A senha deve ter no mínimo 6 caracteres."  
- `password.confirmed`: "A confirmação da senha não confere."


# Models do Projeto Laravel

## 1. FavoriteMovie (Pivot Model)

**Descrição:**  
Representa a tabela intermediária `favorite_movies`, que relaciona usuários e filmes favoritos.

**Características:**  
- Extende `Pivot` pois é uma tabela pivot para relacionamento many-to-many.  
- Usa o trait `HasFactory` para facilitar criação de dados em testes.  
- Define os campos preenchíveis (`fillable`): `user_id` e `movie_id`.

```
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FavoriteMovie extends Pivot
{
    use HasFactory;

    protected $table = 'favorite_movies';

    protected $fillable = [
        'user_id',
        'movie_id',
    ];
}

```

## 2. Genre
**Descrição:**
Model que representa gêneros de filmes.

**Campos preenchíveis:**

-name: nome do gênero.
-tmdb_genre_id: ID do gênero no TMDB.

**Relacionamentos:**

-movies(): relacionamento many-to-many com a model Movie pela tabela movie_genre.


```
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tmdb_genre_id'
    ];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_genre');
    }
}
```

## 3. Movie

**Descrição:**  
Model que representa os filmes.

**Campos preenchíveis:**

- `tmdb_id`: ID do filme no TMDB.
- `title`: título do filme.
- `overview`: resumo/sinopse.
- `poster_path`: caminho para o pôster.
- `release_date`: data de lançamento.

**Relacionamentos:**

- `genres()`: relacionamento many-to-many com `Genre` pela tabela `movie_genre`.
- `favoritedByUsers()`: relacionamento many-to-many com `User` pela tabela `favorite_movies`, incluindo timestamps.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id',
        'title',
        'overview',
        'poster_path',
        'release_date',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movie_genre');
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_movies')->withTimestamps();
    }
}
```

# 4. User

**Descrição:**  
Model que representa os usuários do sistema, estendendo `Authenticatable` para autenticação.

## Campos preenchíveis

- `name`
- `email`
- `password`

## Campos ocultos

- `password`
- `remember_token`

## Casts

- `email_verified_at` convertido para `datetime`.

## Relacionamentos

- `favoriteMovies()`: relacionamento many-to-many com `Movie` pela tabela `favorite_movies`, incluindo timestamps.

```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function favoriteMovies()
    {
        return $this->belongsToMany(Movie::class, 'favorite_movies')->withTimestamps();
    }
}

```

# Serviços (Services)

Os serviços são classes responsáveis pela lógica de negócio da aplicação, organizando operações complexas que envolvem múltiplos modelos, APIs externas e repositórios. A seguir, uma descrição dos principais serviços usados no sistema.

---

## FavoriteMovieService

Serviço que gerencia a lógica relacionada aos filmes favoritos de um usuário.

### Dependências
- **TmdbService:** Comunicação com a API TMDB para obter dados de filmes.  
- **MovieService:** Serviço para salvar filmes e seus gêneros localmente.  
- **MovieRepository:** Acesso direto aos dados dos filmes.  
- **FavoriteMovieRepository:** Acesso e manipulação da tabela de filmes favoritos.

### Principais métodos

- `create(User $user, int $tmdbId): array`  
  Adiciona um filme aos favoritos do usuário, buscando dados na TMDB, armazenando localmente e associando ao usuário.

- `list(User $user, ?int $genreId = null): Collection`  
  Lista os filmes favoritos do usuário, com filtro opcional por gênero.

- `delete(User $user, int $tmdbId): array`  
  Remove um filme dos favoritos do usuário.

- `formatMovieResponse(Movie $movie): array` *(privado)*  
  Formata os dados do filme para resposta na API.

---

## MovieService

Serviço responsável pela persistência dos filmes e seus gêneros na base local.

### Dependência
- **MovieRepository:** Repositório para operações de banco no modelo Movie.

### Principal método
- `storeMovieWithGenres(array $movieData): Movie`  
  Salva ou atualiza um filme e sincroniza os gêneros associados recebidos da API TMDB.

---

## TmdbService

Serviço para comunicação com a API externa TMDB, com uso de cache para otimizar chamadas.

### Principais métodos

- `searchMovies(string $query): array|null`  
  Busca filmes pela query, armazenando resultados em cache por 1 hora.

- `getMovieDetails(int $tmdbId): array|null`  
  Obtém detalhes completos de um filme, com cache de 6 horas.

- `getMovieVideos(int $tmdbId): array|null`  
  Obtém trailers e vídeos relacionados ao filme, cache de 6 horas.

- `getMovieCredits(int $tmdbId): array|null`  
  Obtém elenco e equipe técnica do filme, cache de 6 horas.

- `getFullMovieData(int $tmdbId): array`  
  Retorna os detalhes, vídeos e créditos juntos.

---

## UserService

Serviço que gerencia autenticação e cadastro de usuários.

### Principais métodos

- `create(array $data): array`  
  Registra um novo usuário, com hash da senha, e retorna um token de autenticação.

- `login(array $data): array`  
  Autentica um usuário via email e senha, retornando um token de acesso.

- `logout(User $user): void`  
  Revoga o token de acesso atual do usuário, realizando logout.

# Repositories

Os **repositories** são classes responsáveis pela camada de acesso a dados, encapsulando as operações de consulta, inserção, atualização e deleção no banco. Eles ajudam a organizar o código, separando a lógica de persistência da lógica de negócio, facilitando testes e manutenção.

---

## FavoriteMovieRepository

Este repositório gerencia as operações relacionadas à lista de filmes favoritos de um usuário.

### Principais métodos:

- **addFavorite(User $user, Movie $movie): void**  
  Adiciona um filme à lista de favoritos do usuário, utilizando `syncWithoutDetaching` para preservar outros favoritos existentes.

- **removeFavorite(User $user, Movie $movie): void**  
  Remove um filme da lista de favoritos do usuário com o método `detach`.

- **isFavorite(User $user, Movie $movie): bool**  
  Verifica se um filme já está favoritado pelo usuário, retornando verdadeiro ou falso.

- **listFavorites(User $user, ?int $genreId = null): Collection**  
  Retorna uma coleção dos filmes favoritos do usuário, com opção de filtro por gênero. Usa `whereHas` para filtrar filmes que tenham um gênero específico.

---

## MovieRepository

Este repositório cuida da persistência dos filmes no banco de dados.

### Principais métodos:

- **storeOrUpdate(array $data): Movie**  
  Cria ou atualiza um filme baseado no identificador TMDB (`tmdb_id`), utilizando o método `updateOrCreate`. Isso mantém o banco sincronizado com os dados atualizados da API.

- **findByTmdbId(int $tmdbId): ?Movie**  
  Busca e retorna um filme pela chave TMDB. Retorna `null` caso não encontre.

---

## Vantagens do uso dos Repositories

- Centralizam o acesso e manipulação dos dados, facilitando reutilização.
- Permitem desacoplar a lógica de negócio da lógica de acesso ao banco.
- Facilitam a manutenção e testes, pois mudanças no banco podem ser isoladas nos repositórios.
- Melhoram a legibilidade do código, deixando os serviços mais focados na regra de negócio.

# Trait ApiResponse

O trait **ApiResponse** é uma utilidade que fornece métodos para padronizar respostas JSON em APIs construídas com Laravel. Ele facilita o retorno de respostas bem estruturadas para sucessos e erros nas requisições HTTP.

---

## Métodos principais

### `success($data = null, string $message = 'Success', int $status = 200): JsonResponse`

- Retorna uma resposta JSON indicando sucesso.
- Parâmetros:
  - `$data` (opcional): dados a serem retornados na resposta.
  - `$message` (opcional): mensagem descritiva (padrão: `'Success'`).
  - `$status` (opcional): código HTTP da resposta (padrão: `200`).
- Estrutura da resposta:
  ```json
  {
    "success": true,
    "message": "Success",
    "data": { ... },
    "errors": null
  }
  ```
# APIs do Sistema

Este conjunto de rotas define a interface de comunicação entre cliente e servidor para o sistema de filmes, com autenticação via Sanctum e operações sobre usuários, filmes, gêneros e favoritos.

---

## Rotas Públicas (Sem Autenticação)

### Usuários

- **POST /users**
  - Cria um novo usuário.
  - Controller: `UserController@create`

- **POST /users/login**
  - Autentica um usuário e gera token de acesso.
  - Controller: `UserController@login`

---

## Rotas Protegidas (Requer Token Sanctum)

### Usuário autenticado

- **GET /users/detail**
  - Retorna informações do usuário logado.
  - Controller: `UserController@detail`

- **POST /users/logout**
  - Realiza logout do usuário atual, revogando o token.
  - Controller: `UserController@logout`

---

### Filmes (TMDB)

- **GET /movies/search**
  - Busca filmes na API externa TMDB por termo de pesquisa.
  - Controller: `TmdbController@search`

- **GET /movies/{tmdbId}/detail**
  - Retorna detalhes completos de um filme pelo ID TMDB.
  - Controller: `TmdbController@detail`

---

### Gêneros

- **GET /genres**
  - Lista todos os gêneros disponíveis.
  - Controller: `GenreController@index`

---

### Favoritos

- **POST /favorites**
  - Adiciona um filme aos favoritos do usuário.
  - Controller: `FavoritesMoviesController@create`

- **GET /favorites**
  - Lista os filmes favoritos do usuário, opcionalmente filtrados por gênero.
  - Controller: `FavoritesMoviesController@list`

- **DELETE /favorites/{movie}**
  - Remove um filme da lista de favoritos do usuário.
  - Controller: `FavoritesMoviesController@delete`

---

## Segurança e Autorização

- As rotas públicas são acessíveis sem autenticação, usadas para cadastro e login.
- As demais rotas exigem autenticação via middleware `auth:sanctum`.
- Isso garante que apenas usuários autenticados possam acessar dados protegidos como favoritos, detalhes de usuário e busca avançada de filmes.

---

# Testes do Projeto

## 1. Testes da FavoriteMovieRepository

**Objetivo:**  
Testar as operações relacionadas aos filmes favoritos de um usuário, através do repositório `FavoriteMovieRepository`.

**O que testa:**

- Adicionar um filme aos favoritos.
- Remover um filme dos favoritos.
- Verificar se um filme está marcado como favorito.
- Listar todos os filmes favoritos de um usuário.
- Filtrar filmes favoritos por gênero.

**Importante:**  
Usa `RefreshDatabase` para garantir banco limpo a cada teste, garantindo isolamento.

---

## 2. Testes da MovieRepository

**Objetivo:**  
Testar a criação, atualização e busca de filmes na base local, via `MovieRepository`.

**O que testa:**

- Criar um novo filme caso ele ainda não exista.
- Atualizar um filme existente com dados novos.
- Buscar um filme pelo seu `tmdb_id`.
- Garantir que retorna `null` quando o filme não existe.

**Detalhe:**  
Usa factories para criar os dados, garantindo testes confiáveis e reprodutíveis.

---

## 3. Testes do TmdbService

**Objetivo:**  
Testar a comunicação com a API externa do TMDB (The Movie Database), simulando as respostas HTTP.

**O que testa:**

- Busca de filmes por título (`searchMovies`).
- Consulta de detalhes específicos do filme (`getMovieDetails`).
- Obtenção dos vídeos do filme, como trailers (`getMovieVideos`).
- Consulta ao elenco e equipe de produção (`getMovieCredits`).

**Técnica:**  
Usa `Http::fake()` para simular respostas da API e `Cache::flush()` para garantir ambiente limpo.

---

## 4. Testes do UserService

**Objetivo:**  
Testar funcionalidades do serviço de usuários, como criação de conta e login.

**O que testa:**

- Criar um usuário e garantir que retorna token de autenticação.
- Realizar login com credenciais corretas, garantindo retorno do token.
- Lançar exceção quando o login é feito com senha incorreta.
- Segurança: Confirma que a senha está sendo hasheada corretamente.

---

## Considerações gerais

- Os testes seguem boas práticas de Test Driven Development (TDD).
- Cada teste é isolado e usa a trait `RefreshDatabase` para garantir um banco limpo e consistente.
- Testes de serviços externos (TMDB) usam mocks para não depender da API real.
- Testes garantem tanto o funcionamento das regras de negócio quanto a integração correta com banco e APIs externas.
- São fundamentais para garantir qualidade, evitar regressões e documentar o comportamento esperado do sistema.

# Resumo das Pages do Frontend

- **FavoritesPage.vue**  
  Exibe os filmes favoritos do usuário, com filtro por gênero, visualização detalhada em modal e ações para marcar/desmarcar favoritos. Usa store do usuário e feedback via toast.

- **LoginPage.vue**  
  Permite o login do usuário com formulário simples, validação HTML5, mensagens de erro, controle de carregamento e redirecionamento pós-login. Usa store para autenticação e notificações toast.

- **RegisterPage.vue**  
  Página para criação de nova conta com formulário para nome, email, senha e confirmação. Validação básica, mensagens de erro, feedback visual e redirecionamento para login após cadastro. Usa UserProvider e notificações toast.

- **SearchMoviePage.vue**  
  Permite buscar filmes na base TMDB, exibir resultados em grid, abrir detalhes em modal e favoritar/desfavoritar filmes. Inclui feedback visual de carregamento e uso da store para favoritos. Usa TmdbProvider e notificações toast.
# Resumo dos Providers

## TmdbProvider

- Responsável por buscar filmes na API TMDB via backend.
- Método principal:
  - `searchMovies(query)`: realiza uma requisição GET para `/movies/search` com o texto de busca e retorna uma lista de filmes encontrados.

## UserProvider

- Gerencia operações relacionadas ao usuário, comunicando-se com o backend.
- Métodos principais:
  - `register(userData)`: envia dados para criar um novo usuário.
  - `login(credentials)`: envia credenciais para autenticar o usuário.
  - `getUser()`: obtém detalhes do usuário logado.
  - `logout()`: realiza logout do usuário.
# Resumo da Store `useUserStore`

Esta store, criada com Pinia, gerencia o estado relacionado ao usuário, autenticação e favoritos.

## Estado (`state`)

- `user`: dados do usuário logado.
- `token`: token JWT armazenado no `localStorage`.
- `favorites`: lista de filmes favoritos do usuário.
- `genres`: lista de gêneros de filmes.
- `loadingFavorites`: indicador de carregamento dos favoritos.
- `genre_selected`: gênero atualmente selecionado para filtragem.

## Getters

- `isLoggedIn`: retorna `true` se o usuário estiver autenticado (token presente).
- `isFavorite(movieId)`: verifica se um filme está nos favoritos.

## Persistência

- Os dados `token`, `user`, `favorites`, `genres` e `genre_selected` são persistidos no `localStorage`.

## Ações

- `login(credentials)`: realiza login via `UserProvider`, salva token, busca dados do usuário e favoritos.
- `fetchUser()`: obtém os dados do usuário logado.
- `fetchFavorites(genreId)`: busca filmes favoritos, opcionalmente filtrados por gênero.
- `logout()`: realiza logout, limpa estado e remove token.
- `toggleFavorite(movie)`: adiciona ou remove um filme dos favoritos.
- `fetchGenres()`: carrega gêneros de filmes.
- `initialize()`: inicializa a store com dados existentes no token (configura cabeçalho auth, carrega gêneros, usuário e favoritos).

## Funções auxiliares internas

- `saveToken(token)`: armazena ou remove o token do `localStorage`.
- `setAuthHeader(token)`: configura o cabeçalho Authorization para as requisições axios.

---

Esta store integra chamadas ao backend via `axios` e `UserProvider` para autenticação e gerenciamento dos dados do usuário e seus favoritos, garantindo reatividade e persistência no frontend.

# Justificativa das Escolhas Técnicas e Resultados Obtidos

Este projeto foi desenvolvido atendendo ao desafio técnico de criar uma aplicação de Catálogo de Filmes integrando a API pública do TMDB, com funcionalidades de busca, favoritos e filtragem por gênero, utilizando as tecnologias obrigatórias e respeitando os critérios de avaliação.

---

## Por que as escolhas feitas fazem sentido?

### 1. Backend em Laravel

- **Por quê?** Laravel é um framework PHP moderno, robusto e bastante produtivo para construir APIs RESTful, que foi a base para a comunicação com o frontend e integração com a API do TMDB.
- **Resultado:** Permitiu estruturar facilmente o CRUD dos filmes favoritos, autenticação de usuários, e encapsular a lógica da API externa (TMDB) de forma organizada e segura.

### 2. Frontend com Vue.js

- **Por quê?** Vue.js é uma SPA (Single Page Application) leve, reativa e de fácil aprendizado, ideal para construir interfaces dinâmicas e responsivas.
- **Resultado:** Ofereceu uma boa experiência de usuário para buscas, listagem e manipulação de favoritos, incluindo filtros por gênero, com feedback imediato e carregamento ágil.

### 3. Banco de Dados MySQL

- **Por quê?** MySQL é amplamente suportado, confiável e fácil de configurar, especialmente dentro do ambiente Docker.
- **Resultado:** Armazenamento persistente dos dados locais, como usuários, filmes favoritos e gêneros, garantindo que as informações do usuário sejam mantidas entre sessões.

### 4. Docker e docker-compose

- **Por quê?** Docker garante um ambiente consistente e portátil, eliminando problemas de dependências ou configuração entre diferentes máquinas.
- **Resultado:** Ambiente fácil de subir com um único comando (`docker-compose up -d`), facilitando a instalação e execução da aplicação tanto para desenvolvimento quanto para avaliação.

### 5. Versionamento Git

- **Por quê?** Git é o padrão da indústria para controle de versão, colaborando para um histórico claro e organizado do desenvolvimento.
- **Resultado:** Todo o código está disponível no repositório Git, com commits claros e organizados, facilitando a revisão e manutenção.

### 6. Persistência e gerenciamento local de favoritos

- **Por quê?** Para melhorar a experiência do usuário, armazenar localmente os filmes favoritos e permitir o gerenciamento offline é essencial.
- **Resultado:** Usuários podem adicionar, listar e remover favoritos com filtragem, mesmo sem fazer chamadas repetidas à API externa, reduzindo latência e uso desnecessário da API.

### 7. Integração limpa e modular com a API TMDB

- **Por quê?** Manter a integração com a API TMDB isolada dentro de providers facilita manutenção e futuras expansões.
- **Resultado:** Código organizado, facilmente testável, com abstração clara entre dados locais e dados externos.

---

## Impacto das escolhas no resultado final

- **Funcionalidade completa:** Atende a todos os requisitos do desafio, incluindo busca, CRUD de favoritos, filtro por gênero e autenticação.
- **Estrutura organizada:** Código limpo, dividido em camadas, com uso de stores, providers, controllers e rotas claras.
- **Documentação clara:** README completo com instruções detalhadas para setup, execução, testes e uso da API TMDB.
- **Portabilidade:** Docker torna o projeto simples de instalar e rodar em qualquer ambiente.
- **Experiência do usuário:** Frontend ágil e responsivo, com funcionalidades de favoritos funcionando perfeitamente.
- **Manutenção facilitada:** Uso de boas práticas e organização que facilitam futuras melhorias.

---

## Conclusão

A escolha de tecnologias modernas e consolidadas, aliada à organização do código e ao uso de Docker para simplificar o ambiente, resultou em uma aplicação funcional, fácil de usar e manter, que cumpre com excelência os requisitos do desafio técnico.

Essa abordagem demonstra domínio das ferramentas e boas práticas de desenvolvimento Full Stack, adequadas ao perfil de um Desenvolvedor(a) Full Stack Júnior.
