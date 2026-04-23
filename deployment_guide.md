# Deployment Guide: Water Refilling System on Railway

This guide provides step-by-step instructions to deploy your Laravel-based Water Refilling System to [Railway](https://railway.app/).

## Prerequisites

1.  A [GitHub](https://github.com/) account.
2.  A [Railway](https://railway.app/) account.
3.  Your project pushed to a GitHub repository.

---

## Step 1: Prepare Your Repository

Ensure your project contains the following files (already added):
- `Procfile`: Tells Railway how to run the web server.

**Push your latest changes to GitHub:**
```bash
git add .
git commit -m "chore: add Railway configuration"
git push origin main
```

---

## Step 2: Create a New Project on Railway

1.  Log in to [Railway](https://railway.app/).
2.  Click **+ New Project**.
3.  Select **Deploy from GitHub repo**.
4.  Choose your repository.
5.  Click **Deploy Now**. (The first build might fail due to missing environment variables, which is normal).

---

## Step 3: Add a MySQL Database

1.  In your Railway project dashboard, click **+ Add Service**.
2.  Select **Database** and then **MySQL**.
3.  Wait for the database to be provisioned.
4.  Railway will automatically provide a `DATABASE_URL` variable, which Laravel will use.

---

## Step 4: Configure Environment Variables

1.  Go to your **Web Service** (the one deployed from GitHub).
2.  Click on the **Variables** tab.
3.  Add the following variables:

| Variable | Recommended Value |
| :--- | :--- |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Generate one locally using `php artisan key:generate --show` or copy from your `.env`. |
| `APP_URL` | Your Railway app URL (e.g., `https://your-app-production.up.railway.app`) |
| `DB_CONNECTION` | `mysql` |
| `LOG_CHANNEL` | `stderr` (Better for Railway logs) |
| `SESSION_DRIVER` | `cookie` or `database` |
| `TRUSTED_PROXIES` | `*` |

> [!TIP]
> Railway automatically injects `DATABASE_URL`, `DB_HOST`, `DB_PORT`, etc., when you link the MySQL service.

---

## Step 5: Finalize Deployment & Migrations

1.  Railway will automatically re-deploy when you update variables.
2.  To run migrations, you have two options:
    - **Option A (One-time):** Go to the **Settings** of your service, find the **Deploy** section, and add a custom **Post-Deploy Command**: `php artisan migrate --force`.
    - **Option B (Manual):** Use the Railway CLI or the **Terminal** tab in the Railway UI to run `php artisan migrate --force`.

---

## Step 6: Verify the App

1.  Once the build is successful, click the public URL provided by Railway.
2.  Test the following:
    - Home page loads.
    - Login/Registration works.
    - Creating an order (ensure DB connection is working).
    - Assets (CSS/JS) are loading correctly.

---

## Troubleshooting

- **404 on Assets:** Ensure `npm run build` ran during deployment (handled by `nixpacks.toml`).
- **Mixed Content Warnings:** Ensure `APP_URL` starts with `https://`.
- **Database Connection Refused:** Ensure the MySQL service is properly linked to your web service in Railway.
