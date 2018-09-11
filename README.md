## Структура проекта

| Файл / Каталог | Краткое описание / назначение |
| ------ | ------ |
| index.php | Входная точка приложения. |
| app/models | Взаимодействие с базой данных. |
| app/views | Отображение данных клиенту. |
| app/controllers | Контроль принятых данных от пользователя. Обеспечение передачи данных из модели в слой представления. |
| app/uploaders | Обработка и загрузка файлов на сервер. |
| app/db | Миграции БД. |
| app/frontend | Клиентское приложение, реализующее взаимодействие с API. |
| config | Настройки развертывания через Capistrano. |

## Запуск приложения

### Backend API

<p><details>
  <summary><b>Пример конфигурации Apache (.htaccess)</b></summary>

```shell
RewriteEngine On
DirectorySlash Off
RewriteCond %{REQUEST_URI} !=/index.php
RewriteCond %{REQUEST_URI} !.*\.png$ [NC]
RewriteCond %{REQUEST_URI} !.*\.jpg$ [NC]
RewriteCond %{REQUEST_URI} !.*\.gif$ [NC]
RewriteCond %{REQUEST_URI} !.*\.svg$ [NC]
RewriteCond %{REQUEST_URI} !.*\.tiff$ [NC]
RewriteRule .* /index.php
```

</details></p>

<p><details>
  <summary><b>Пример конфигурации nginx (локейшены)</b></summary>

```shell
location / {
  try_files $uri $uri/ @rewrites;
}

location @rewrites {
  rewrite ^(.+)$ /index.html last;
}

location ~* \.php$ {
  try_files $uri = 404;
  fastcgi_split_path_info ^(.+\.php)(/.+)$;
  fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
  fastcgi_index index.php;
  fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
  include fastcgi_params;
}
```

</details></p>

<p><details>
  <summary><b>Переменные окружения</b></summary>
  
  Необходимо определить значения для следующих переменных окружения:

```shell
DB_HOST - хост базы данных
DB_NAME - название БД
DB_USER - пользователь БД
DB_PASS - пароль пользователя
UPLOADS_HOST - хост, через который осуществляется доступ к загруженным файлам (можно вынести на отдельный домен)
```

</details></p>

Перед запуском приложения, необходимо выполнить все SQL миграции БД.

### Client JS App (VueJS)

</details></p>

<p><details>
  <summary><b>Запуск локально</b></summary>

```shell
cd ./app/frontend
npm install
npm run serve
```

</details></p>

<p><details>
  <summary><b>Сборка production билда</b></summary>

```shell
cd ./app/frontend
npm install
npm run build
```

</details></p>
