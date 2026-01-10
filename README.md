# MiPhantDB

**MiPhantDB** é uma biblioteca **leve, fluente e orientada a código escrita em PHP** para abstração de banco de dados **MySQL/MariaDB**, focada em **simplicidade**, **controle explícito das queries** e **baixo overhead**.

Oferece uma **API encadeável (fluent interface)** para construção de SQL **legível**, **previsível** e **flexível**, mantendo o desenvolvedor no controle total da query final.

Ideal para projetos que precisam de **produtividade**, **performance**, **CLIs**, **APIs**, **sistemas legados** e **ambientes com pouco recurso**.

---

## ✨ Características

* Abstração leve sobre **MySQLi**
* API fluente (encadeável)
* Suporte a **SELECT, INSERT, UPDATE, DELETE**
* Suporte a **prepared statements**
* Construção dinâmica de `WHERE`, `ORDER BY` e `LIMIT`
* Criação e alteração de tabelas via código
* Suporte a `INNER JOIN`
* Modo **sandbox** para debug
* Zero dependências externas
* Compatível com **PHP 8.4 ou superior**

---

## 📦 Instalação

### Via Composer (recomendado)

```bash
composer require mugomes/miphantdb
```

### Manual

Copie os arquivos da pasta `MiPhantDB` para o seu projeto e utilize o autoload ou `require`.

---

## 🔌 Conexão com o banco

```php
use MiPhantDB\database;

$db = new database([
    'server'   => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'meubanco'
], true); // true ativa o modo sandbox (logs em tela)
```

---

## 📖 SELECT

```php
use MiPhantDB\select;

$select = new select([
    'server'   => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'meubanco'
]);

$select->table('users')
    ->column('id')
    ->column('name')
    ->where('status', 'ativo')
    ->orderby('name')
    ->limit(0, 10)
    ->select();

while ($row = $select->fetch()) {
    echo $row['name'];
}

$select->close();
```

---

## 🔐 Prepared Statements

```php
$select->table('users')
    ->where('id', '?')->prepared(1, 'i')
    ->select();

$select->getResult();

$row = $select->fetch();
```

---

## ➕ INSERT

```php
use MiPhantDB\insert;

$insert = new insert($dbConfig);

$insert->table('users')
    ->add('name', '?')
    ->add('email', '?')
    ->prepared('Murilo', 's')
    ->prepared('murilo@email.com', 's')
    ->insert();

$id = $insert->idinsert();
```

---

## ✏️ UPDATE

```php
use MiPhantDB\update;

$update = new update($dbConfig);

$update
    ->table('users')
    ->add('email', '?')
    ->where('id', '?')
    ->prepared('novo@email.com', 's')
    ->prepared(1, 'i')
    ->update();
```

---

## ❌ DELETE

```php
use MiPhantDB\delete;

$delete = new delete($dbConfig);

$delete->table('users')
    ->where('id', 1)
    ->delete();
```

---

## 🧱 Criação de tabelas

```php
use MiPhantDB\table;

$table = new table($dbConfig);

$table->table('users')
    ->int()->autoIncrement()->primaryKey()->add('id')
    ->varcharSize(100)->add('name')
    ->varcharSize(150)->add('email')
    ->engine('InnoDB')
    ->create();
```

---

## 🔧 Alterar tabela

```php
$table
    ->table('users')
    ->varcharSize(255)->add('bio')
    ->alter(table::ALTER_ADD);
```

---

## 🔍 Verificar se coluna existe

```php
if (!$table->columnExists('email')) {
    // criar coluna
}
```

---

## 🔒 Encerrando a conexão (`close`)

Após executar as operações no banco de dados, é recomendável encerrar explicitamente a conexão para liberar recursos de memória e resultados pendentes.

```php
$select->close();
```

### O que o `close()` faz?

* Libera automaticamente o **result set** (`mysqli_free_result`) quando aplicável
* Finaliza corretamente **prepared statements**
* Encerra a conexão ativa com o banco (`mysqli_close`)
* Evita vazamento de memória em scripts longos ou CLIs

### Quando usar?

* Após finalizar uma consulta `CREATE`, `ALTER`, `SELECT`, `INSERT`, `UPDATE` ou `DELETE`
* Em **scripts CLI**, workers e processos de longa duração
* Em loops ou execuções repetidas de queries

### Exemplo completo

```php
$select->table('users')
    ->where('status', 'ativo')
    ->select();

while ($row = $select->fetch()) {
    echo $row['name'];
}

$select->close();
```

> 💡 **Dica:**
> Embora o PHP feche conexões automaticamente ao final do script, o uso explícito de `close()` é uma boa prática para garantir desempenho e previsibilidade.

---

## 🧠 Outras Informações

* Sem ORM pesado
* Sem reflexão ou proxies mágicos
* SQL continua sendo SQL
* Código previsível e fácil de depurar
* Ideal para quem **gosta de controle**

---

## 👤 Autor

**Murilo Gomes Julio**

🔗 [https://mugomes.github.io](https://mugomes.github.io)
📺 [https://youtube.com/@mugomesoficial](https://youtube.com/@mugomesoficial)

---

## 🤝 Support

* GitHub Sponsors: [https://github.com/sponsors/mugomes](https://github.com/sponsors/mugomes)
* Apoie o projeto: [https://mugomes.github.io/apoie.html](https://mugomes.github.io/apoie.html)

---

## 📜 License

Copyright (c) 2025-2026 Murilo Gomes Julio

Licensed under the [MIT](https://github.com/mugomes/miphantdb/blob/main/LICENSE).

All contributions to the MiPhantDB are subject to this license.
