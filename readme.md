**Маршруты:** 

`GET|POST /api/addlink` с параметром url - генерация ссылки для урла url

`GET /{hash}` - перейти по ссылке

Количество просмотров инкрементится в базе при каждом открытии ссылки.

Если ссылка с идентичным урлом уже есть в базе, то отдаётся хеш от неё.

CRUD для удаления, изменения маршрутов я делать не стал, т.к. для этого нужно прикручивать авторизацию, права на ссылки и т.д. Если нужно, могу сделать.


**Дамп для таблиц базы из 2 пункта после оптимизации:**

```
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `title` text DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY(id),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL COMMENT 'user unique identifier in the system',
  `is_active` int(11) DEFAULT NULL COMMENT 'can be only 0 - false or 1 - true, default true',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY(id),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

Я не стал добавлять внешний и unique ключи для полей `posts.user_id` и `users.email` соответственно, т.к. цель именно сделать максимально быструю выборку.