FROM php:7.3.10-apache

# APT 自动安装 PHP 相关的依赖包,如需其他依赖包在此添加
RUN apt-get update \
    && apt-get install -y \
        libmcrypt-dev \
        libz-dev \
        git \
        wget \
        libpcre3-dev \
        libzip-dev \
        apt-utils \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    # 官方 PHP 镜像内置命令，安装 PHP 依赖
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install \
        mbstring \
        pdo_mysql \
        zip \
    && pecl install mcrypt-1.0.3 \
    && docker-php-ext-enable mcrypt \
    # 用完包管理器后安排打扫卫生可以显著的减少镜像大小
    && apt-get clean \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    # 安装 Composer，此物是 PHP 用来管理依赖关系的工具
    && curl -sS https://getcomposer.org/installer \
        | php -- --install-dir=/usr/local/bin --filename=composer \
    # 配置国内镜像
    && composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
# 开启 URL 重写模块
# 配置默认放置 App 的目录
RUN a2enmod rewrite \
    && mkdir -p /app 
# 复制代码到 App 目录
COPY . /app

WORKDIR /app

# 执行 Composer 安装依赖
RUN composer install --optimize-autoloader

# 连接入口文件
RUN rm -fr /var/www/html \
    && ln -s /app/public /var/www/html

EXPOSE 80