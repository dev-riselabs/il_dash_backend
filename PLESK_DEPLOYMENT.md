Plesk Deployment Checklist for il_dash_backend

Prerequisites
- PHP 8.2+ with extensions: pdo_mysql, mbstring, openssl, xml, ctype, json, fileinfo, curl, tokenizer
- Composer available on the server (or use Plesk Composer UI)
- MySQL server and credentials
- Node/NPM if you need to build frontend assets on the server (optional; CI build recommended)

1) Plesk domain setup
- Set domain / subdomain Document root to: `/<path-to-repo>/il_dash_backend/public`
- Set PHP version to 8.2 (per-domain PHP settings)

2) Deploy code
- Upload project files to server (git, FTP, or Plesk Git deployment)
- Ensure `vendor/` present or run composer (see step 3)

3) Composer & vendor
From the project root (il_dash_backend):

```bash
cd /path/to/il_dash_backend
composer install --no-dev --optimize-autoloader
```

If Composer is not available via SSH, enable Plesk Composer support or build vendor locally and upload.

4) Environment
- Copy `.env.example` to `.env` and update values (DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_ENV=production, APP_DEBUG=false, APP_URL)

```bash
cp .env.example .env
php -d memory_limit=-1 artisan key:generate
```

5) Database
- Ensure `pdo_mysql` extension is enabled in PHP
- Configure MySQL credentials in `.env`
- Run migrations:

```bash
php artisan migrate --force
```

6) Storage & permissions
- Create storage link and set permissions:

```bash
php artisan storage:link
chown -R www-data:www-data storage bootstrap/cache     # adjust user/group for your host
chmod -R 775 storage bootstrap/cache
```

7) Caching & optimizations
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

8) Scheduler
- Add a Scheduled Task in Plesk or cron (run every minute):

```
* * * * * cd /path/to/il_dash_backend && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

9) Queue workers
- The app supports `database` and `redis` queues (see `.env` and `config/queue.php`).
- For production, run a persistent worker via Plesk Process Manager or external supervisor:

```bash
cd /path/to/il_dash_backend && /usr/bin/php artisan queue:work --sleep=3 --tries=3 --timeout=0
```

- If using Redis, ensure Redis server available and PHP has `phpredis` OR use `predis/predis` (composer dependency). Set `QUEUE_CONNECTION=redis` in `.env`.

10) Assets (optional)
- If backend needs frontend assets built on server:

```bash
npm ci
npm run build
```

Recommendation: build frontend in CI and deploy static assets with the backend.

11) Post-deploy checks
- Visit `APP_URL` and confirm pages load
- Check logs: `storage/logs/laravel.log`
- Verify queue jobs are processed and scheduler runs

Notes & gotchas
- Document root must point to `public` to avoid exposing application code
- Ensure `APP_KEY` is set and `APP_DEBUG=false` in production
- If you prefer SQLite for small installs, adjust `DB_CONNECTION` accordingly (not recommended for multi-user production)

Contact
- If you want, I can provide a Plesk UI step-by-step or generate a shell script to run these commands automatically.