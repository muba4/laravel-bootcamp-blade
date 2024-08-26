@setup
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
@endsetup

@servers(['laravel' => env('CICD_SERVER')])

@task('deploy', ['on' => 'laravel'])
    set -e

    cd {{env('CICD_PATCH')}}

    echo "Начало накатывания обновлений с сайта разработки...."

    git stash
    git pull origin main
    php artisan down

    composer install --no-dev --optimize-autoloader
    npm install

    php artisan migrate --force

    php artisan config:cache
    php artisan event:cache
    php artisan route:cache
    php artisan view:cache
    npm run build

    php artisan up

    echo "Обновления с сайта разработки накачены! Ура! Ура!! =D"
@endtask