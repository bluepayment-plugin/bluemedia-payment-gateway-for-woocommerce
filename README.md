### Uruchomienie środowiska developerskiego

Środowisko uruchamiamy komendą `docker-compose up` lub `docker-compose start` w głównym katalogu. 

Pierwsze uruchomienie zainstaluje Wordperss + Woocommerce. Instalacja opiera się o przygotowany `docker/wp-config.php` oraz o dump bazy danych w katalogu `wp-data`.

### Praca z kodem

Domyślnie podmontowane są 3 katalogi:
- `wp-app` - pełny kod Wordpress,
- `woocommerce` - kod wtyczki WooCommerce,
- `bluepayment` - kod wtyczki płatności BM.

### Przydatne komendy

- `docker-compose stop` - zatrzymanie kontenerów,
- `docker-compose down` - zatrzymanie oraz usunięcie kontenerów,
- `docker-compose down --volumes` - zatrzymanie oraz usunięcie kontenerów wraz z volumenem,

### Front, Admin, phpMyAdmin

Front można podejżeć pod adresem http://localhost

Admin panel pod adresem http://localhost/wp-admin

Dane do logowania dla admina:
- login `wordpress`
- hasło `wordpress`

phpMyAdmin pod adresem http://localhost:8080

Dane do logowania do phpMyAdmin:
- login `root`
- hasło `password`

### informacja o wersji

Wordpress w wersji 5.3.2 na PHP 7.3 / MySQL 5.7

WooCommerce automatycznie instalowany w wersji najnowszej dla danej wersji Wordpress.

##### Dane dostepowe do testówki

- front  `https://bluepayment-woocommerce.bm.bsbox.pl/`
- backend `https://bluepayment-woocommerce.bm.bsbox.pl/wp-admin`
- login/hasło:  `admin/0123456789`

##### K8S

- konfiguracja w kalatlogu `k8s` , 
- obraz bazowy `harbour-ndi.bsbox.pl/internal-read/woocommerce:php7.3`
- build automatyczny z poziomu gitlaba CI. 
- Wypychany obraz znajduje się na registry gitlaba z tagiem latest (do przeniesienia na harbora)
