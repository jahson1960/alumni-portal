# RBSN Alumni Portal

A PHP MVC (custom, no framework) alumni portal for Rome Business School Nigeria, styled with a compiled Tailwind CSS build and backed by MySQL/MariaDB. Admins manage homepage content, news, jobs, events, and resources from an admin panel; alumni can register, log in, and manage their own directory profile.

## Stack

- PHP 8.2, custom MVC (`app/Core` — Router, Controller, Model, Auth, Csrf, Upload)
- MySQL / MariaDB (PDO, prepared statements)
- Tailwind CSS (compiled via the Tailwind CLI, not the CDN build)
- Plain PHP views (no template engine/dependency)

## Local Setup (XAMPP)

0. **Config** — Copy `config/config.example.php` to `config/config.php` and adjust the `db` credentials/port for your environment. `config/config.php` is gitignored since it holds local DB settings.

1. **Database** — Import the schema:
   ```
   C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 -P 3307 < database/schema.sql
   ```
   > **Note on the port:** this machine already has a separate MySQL 8.0 Windows service (`MySQL80`) occupying the default port 3306. XAMPP's bundled MariaDB has been configured (see `C:\xampp\mysql\bin\my.ini`, `[mysqld]` section) to listen on **3307** instead, and `config/config.php` points there. If you later free up 3306 (or you're deploying somewhere without that conflict), change both `my.ini`'s `[mysqld] port` and `config/config.php`'s `db.port` back to `3306` (or remove the `port` key entirely for the default).

2. **Start services** — In the XAMPP Control Panel, start Apache and MySQL as usual. On this machine MySQL must be started with the custom `my.ini` (the Control Panel does this automatically); if starting manually: `C:\xampp\mysql\bin\mysqld.exe --defaults-file=C:\xampp\mysql\bin\my.ini --standalone`.

3. **Tailwind CSS** — Install dependencies and build once:
   ```
   npm install
   npm run build
   ```
   While actively changing view markup/classes, run `npm run watch` instead to rebuild `assets/css/app.css` automatically.

4. **Visit the site** — `http://localhost/alumni%20portal/`. `index.php` is the front controller and lives at the project root (not in a `public/` subfolder), so on a real deploy the domain's document root should point straight at this folder.

## Seeded Accounts

| Role   | Email                          | Password      |
|--------|---------------------------------|---------------|
| Admin  | icebestlimited2@gmail.com       | ChangeMe@123  |
| Alumni | chidi.okonkwo@example.com       | ChangeMe@123  |

**Change the admin password after first login** — there's no in-app password-change flow yet, so update it directly for now:
```sql
UPDATE users SET password_hash = '<new bcrypt hash>' WHERE email = 'icebestlimited2@gmail.com';
```
Generate a hash with: `C:\xampp\php\php.exe -r "echo password_hash('yourNewPassword', PASSWORD_DEFAULT);"`

## Admin Panel

`http://localhost/alumni%20portal/admin/login` — manage:
- **Site Settings** — homepage hero text, mentorship CTA banner, footer metrics bar
- **News & Blog**, **Jobs**, **Events**, **Resources** — full CRUD, with image upload or external image URL for news
- **Alumni** — view profiles, suspend/activate/delete accounts

## Project Layout

`index.php` (project root) is the front controller — everything is routed through it via `.htaccess`. `assets/` is the web-servable build output + uploads folder (`npm run build` writes `assets/css/app.css`; user uploads land under `assets/uploads/`). See `app/` for MVC code, `routes/web.php` for the route table, `database/schema.sql` for the schema + seed data, and `resources/css/app.css` / `tailwind.config.js` for the Tailwind source.

## Deployment

The document root should be this project's root folder itself (not a `public/` subfolder — there isn't one). `.htaccess` routes all requests through `index.php` and explicitly blocks direct access to `app/`, `config/`, `database/`, `routes/`, `resources/`, `node_modules/`, `.git/`, and common non-web file extensions (`.sql`, `.md`, `.json`, `.lock`, `.env`, `.log`), so those stay safe to leave alongside `index.php` even though they're technically inside the served folder.

On Hostinger (or similar) using Git-based deploys: point the site/subdomain at wherever the repo gets cloned to, with no extra subfolder in the path. Remember to also create `config/config.php` on the server (copy from `config/config.example.php` — it's gitignored and won't arrive via git) with the production DB credentials, and run `database/schema.sql` against the production database before first use.
