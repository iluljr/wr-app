module.exports = {
  apps: [
    {
      name: "wr-app",
      script: "artisan",
      interpreter: "php",
      args: ["serve", "--host=0.0.0.0", "--port=8001"],
      env: {
        APP_ENV: "production",
        APP_DEBUG: false,
        APP_TIMEZONE: "Asia/Jakarta",
        TZ: "Asia/Jakarta"
      }
    },
    {
      name: "wr-queue",
      script: "artisan",
      interpreter: "php",
      args: ["queue:work", "--sleep=3", "--tries=3"],
      env: {
        APP_ENV: "production",
        APP_DEBUG: false,
        APP_TIMEZONE: "Asia/Jakarta",
        TZ: "Asia/Jakarta"
      }
    }
  ]
};
