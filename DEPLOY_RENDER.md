# GradeFlow on Render

The repository now includes a Docker web service and Render Blueprint. It needs a persistent MySQL database hosted online; the XAMPP database on your computer cannot be used by the deployed app.

1. Push this project to a **private** Git repository. Keep `.env`, database dumps, and student records out of the repository.
2. Create a persistent MySQL database with your chosen provider. Create a database and user with access to it. Keep the connection URL private. A typical URL is `mysql://USER:PASSWORD@HOST:3306/DATABASE` (URL-encode special characters in the password).
3. In Render, create a **Blueprint** from the repository. Render reads `render.yaml` and builds the Docker image.
4. Set the Blueprint secrets when prompted:
   - `APP_KEY`: generate locally with `php artisan key:generate --show`.
   - `DB_URL`: the online MySQL connection URL, **not** `127.0.0.1` or XAMPP.
   - `ADMIN_EMAIL`: email for the first administrator.
   - `ADMIN_PASSWORD`: a unique password of at least 12 characters.
5. Deploy and open the assigned `https://...onrender.com/admin/login` URL. The first start runs migrations and creates the first administrator only if none exists. Later starts do not overwrite that account.

The Blueprint uses a free web-service plan for a group demo. Free services can sleep, and the separate database may have its own costs or limits. Do not use a short-lived or unbacked database for real student records. The app is configured with `APP_DEBUG=false`, database sessions and cache, and HTTPS URLs. Do not run `DemoDataSeeder` on a live student system.

This setup creates an **empty online database** unless you separately import records. Do not upload a copy of the local XAMPP database without checking privacy and removing demonstration accounts first. If you change the Dockerfile or `render.yaml`, redeploy from Render.
