-- Helper script to create wr_app database and user (adjust as needed)
-- Run in psql as a superuser:
--   psql -U postgres -h 127.0.0.1 -p 5432 -f database/init/create_wr_app_db.sql

DO $$
BEGIN
   IF NOT EXISTS (
      SELECT FROM pg_catalog.pg_roles WHERE rolname = 'admin_wr_app') THEN
      CREATE ROLE admin_wr_app LOGIN PASSWORD 'pass123';
   END IF;
END
$$;

DO $$
BEGIN
   IF NOT EXISTS (
      SELECT FROM pg_database WHERE datname = 'wr_app') THEN
      CREATE DATABASE wr_app OWNER admin_wr_app;
   END IF;
END
$$;

GRANT ALL PRIVILEGES ON DATABASE wr_app TO admin_wr_app;
