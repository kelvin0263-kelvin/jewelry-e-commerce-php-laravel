#!/usr/bin/env bash
set -e

# Render 会给你 PORT（默认 10000），你的 Web server 必须 bind 到它 :contentReference[oaicite:3]{index=3}
APACHE_PORT="${PORT:-10000}"

# 让 Apache 监听正确端口
sed -i "s/Listen 80/Listen ${APACHE_PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${APACHE_PORT}>/" /etc/apache2/sites-available/000-default.conf

# 生产环境常用：缓存配置/路由/视图
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# 迁移：你想自动跑就保留，不想就注释掉下一行
# php artisan migrate --force || true

# 启动 Apache
apache2-foreground
