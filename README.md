Требования:

- nginx/apache
- mysql 5.7+
- redis

Инструкция по запуску:

1. Переименовываешь .env.example в .env
2. В нем же вносишь данные по бд
3. php artisan key:generate
4. php artisan migrate
5. php artisan db:seed
6. В domain/admin можно войти через доступы:
- логин admin@admin.com
- пароль password
7. В domain/admin/user/create создать для себя пользователя и дать все права

После залива данных со старого сайта на новый, нужно зайти с админки

domain/admin/categorydescription/reorder и внизу нажать "сохранить", сформируется порядок категорий
