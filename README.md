# mvc_php

## Параметры для .env

### _Framework
DEBUG=true  
ENV=prod  

### _Language
LANGUAGE="ru"  

### _Databases
DB_DRIVER=""  
DB_NAME=''  
DB_HOST=''  
DB_PORT=''  
DB_USERNAME=""  
DB_PASSWORD=""  
DB_TABLE_PREFIX=""  

### _Urls
FRONTEND_URL="http://mysyte/"  
BACKEND_URL="/admin"  
STORAGE_URL="/storage"  

### _Other
SITE_NAME=""  

## Helper migration

### Database  
      db:create          Создайте новую схему базы данных.  
      db:table           Извлекает информацию из выбранной таблицы  
      db:drop            Удаление базы данных.  
      migrate            Находит и запускает миграцию из указанной папки плагина.  
      migrate:refresh    Выполняет откат последнего обновления для обновления текущего состояния базы данных.  
      migrate:rollback   Запускает метод 'down' для отката последней операции миграции.  

### Generators  
      make:controller    Генерирует новый файл контроллера.  
      make:model         Генерирует новый файл модели.  
      make:migration     Создает новый файл миграции.   
      
### Другое  
      list:migrations    Показывает все существующие файлы миграций.  
