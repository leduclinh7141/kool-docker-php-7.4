server {
    listen @{{ .Env.NGINX_LISTEN }} default_server;
    server_name _;
    root @{{ .Env.NGINX_ROOT }};
    index @{{ .Env.NGINX_INDEX }};
    charset utf-8;

    location = /favicon.ico { log_not_found off; access_log off; }
    location = /robots.txt  { log_not_found off; access_log off; }

    client_max_body_size @{{ .Env.NGINX_CLIENT_MAX_BODY_SIZE }};

    error_page 404 /index.php;

    location / {
        try_files $uri $uri/ /@{{ .Env.NGINX_INDEX }}?$query_string;

        add_header X-Served-By kool.dev;
    }

    location ~ \.php$ {
        fastcgi_buffers @{{ .Env.NGINX_FASTCGI_BUFFERS }};
        fastcgi_buffer_size @{{ .Env.NGINX_FASTCGI_BUFFER_SIZE }};
        fastcgi_pass @{{ .Env.NGINX_PHP_FPM }};
        fastcgi_read_timeout @{{ .Env.NGINX_FASTCGI_READ_TIMEOUT }};
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    # basic H5BP suggestions
    include h5bp/internet_explorer/x-ua-compatible.conf;
    include h5bp/security/referrer-policy.conf;
    include h5bp/security/x-content-type-options.conf;
    include h5bp/security/x-frame-options.conf;
    include h5bp/security/x-xss-protection.conf;

    # performance enhancements (mostly for caching static data)
    include h5bp/web_performance/cache-file-descriptors.conf;
    include h5bp/web_performance/pre-compressed_content_gzip.conf;
}
