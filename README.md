# 📌 **DevsBook Backend API**

Esta API gerencia autenticação, usuários, postagens e interações em um feed social.

## 🔑 **Autenticação**

| Método  | Endpoint              | Descrição                                     |
| -------- | --------------------- | ----------------------------------------------- |
| `POST` | `/api/auth/login`   | Autentica um usuário (`email`,`password`). |
| `POST` | `/api/auth/logout`  | Faz logout do usuário autenticado.             |
| `POST` | `/api/auth/refresh` | Renova o token de autenticação.               |

---

## 👤 **Usuário**

| Método  | Endpoint                     | Descrição                                                                                                |
| -------- | ---------------------------- | ---------------------------------------------------------------------------------------------------------- |
| `POST` | `/api/user`                | Cria um novo usuário (`name`,`email`,`password`,`birthdate`).                                     |
| `PUT`  | `/api/user`                | Atualiza perfil do usuário (`name`,`email`,`birthdate`,`city`,`password`,`password_confirm`). |
| `POST` | `/api/user/avatar`         | Atualiza o avatar do usuário (`avatar`).                                                                |
| `POST` | `/api/user/cover`          | Atualiza a foto de capa (`cover`).                                                                       |
| `GET`  | `/api/user`                | Obtém os dados do usuário autenticado.                                                                   |
| `GET`  | `/api/user/{id}`           | Obtém os dados de um usuário específico.                                                                |
| `POST` | `/api/user/{id}/follow`    | Segue ou deixa de seguir um usuário (**requer autenticação** ).                                   |
| `GET`  | `/api/user/{id}/followers` | Obtém a lista de seguidores de um usuário.                                                               |
| `GET`  | `/api/user/{id}/photos`    | Obtém as fotos postadas por um usuário.                                                                  |

---

## 📰 **Feed e Postagens**

| Método  | Endpoint                         | Descrição                                                 |
| -------- | -------------------------------- | ----------------------------------------------------------- |
| `GET`  | `/api/feed?page={n}`           | Obtém o feed geral, paginado.                              |
| `GET`  | `/api/user/feed?page={n}`      | Obtém o feed do usuário autenticado.                      |
| `GET`  | `/api/user/{id}/feed?page={n}` | Obtém o feed de um usuário específico.                   |
| `POST` | `/api/feed`                    | Cria uma postagem (`type=text/photo`,`body`,`photo`). |
| `POST` | `/api/post/{id}/like`          | Curte ou remove curtida de uma postagem.                    |
| `POST` | `/api/post/{id}/comment`       | Adiciona um comentário em uma postagem (`txt`).          |

---

## 🔍 **Pesquisa**

| Método | Endpoint                | Descrição                                 |
| ------- | ----------------------- | ------------------------------------------- |
| `GET` | `/api/search?txt={q}` | Pesquisa usuários ou postagens pelo texto. |

---

## 📦 **Pacotes Instalados**

* [`<span>tymon</span><span>/jwt</span><span>-auth</span>` (2.1.1)](https://github.com/tymondesigns/jwt-auth) - Gerenciamento de autenticação JWT.
* [`<span>intervention</span><span>/image</span>`]() - Manipulação de imagens (avatar/capa).
