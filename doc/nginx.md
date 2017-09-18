server {
		listen 8012;
	client_max_body_size 0;
		index index.php index.html index.htm index.nginx-debian.html;
	root /home/master/pro/php/kshop/kshopadmin/web;

		location / {
				try_files $uri $uri/ =404;
		}
	location ~ ^/api/(.*) {
		rewrite ^/api/(.*) /$1 break;
		proxy_pass http://localhost:8011;
	}
	location ~ ^(?!/(index.php|assets|favicon.ico))(/.*) {
				rewrite ^(?!/index.php)(/.*) /index.php$2 last;
		}


	location ~ \.php($|/) {
				include snippets/fastcgi-php.conf;
				fastcgi_pass unix:/var/run/php/php5.6-fpm.sock;
		}
}
