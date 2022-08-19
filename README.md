# Instrukcja modułu BluePayment dla platformy WooCommerce

## Podstawowe informacje

BluePayment to moduł płatności umożliwiający realizację transakcji bezgotówkowych w sklepie opartym na platformie WordPress (WooCommerce). Jeżeli jeszcze nie masz wtyczki, możesz ją pobrać [tutaj.](https://github.com/bluepayment-plugin/bluemedia-payment-gateway-for-woocommerce/archive/refs/heads/master.zip)

### Główne funkcje

Do najważniejszych funkcji modułu zalicza się:
- realizację płatności online poprzez odpowiednie zbudowanie startu transakcji
- obsługę powiadomień o statusie transakcji (notyfikacje XML)
- obsługę wielu sklepów jednocześnie z użyciem jednego modułu
- obsługę zakupów bez rejestracji w serwisie
- obsługę dwóch trybów działania – testowego i produkcyjnego (dla każdego z nich wymagane są osobne dane kont, po które zwróć się do nas)
- obsługę popularnych metod płatności, w tym Google Pay, Pay Smartney (płatności odroczone – które pozwalają Twoim klientom płacić za zakupy w wygodny sposób
- możliwość korzystania z BLIKA na dwa sposoby (z przekierowaniem na stronę eblik.pl lub jako BLIK 0 – z wpisaniem kodu na stronie sklepu)
- wybór banku po stronie sklepu i bezpośrednie przekierowanie do płatności w wybranym banku

### Wymagania

- WordPress – przetestowane na wersjach od 4.4 do 6.0.1
- Wtyczka WooCommerce – przetestowano na wersjach od 2.1 do 6.8.0
- Wersja PHP zgodna z wymaganiami względem danej wersji systemu WordPress/Commerce

## Instalacja modułu
1. Pobierz wtyczkę.
2. Zaloguj się do swojego panelu administracyjnego w serwisie WordPress – używając loginu i hasła. 
3. W menu po lewej stronie wybierz Wtyczki ➝ Dodaj nową.
4. Zainstaluj wtyczkę wybierając jedną z poniższych metod.

### Instalacja manualna

1. Kliknij Wyślij wtyczkę na serwer.
2. Wybierz plik z wtyczką z rozszerzeniem .zip
3. Kliknij Zainstaluj teraz, żeby wgrać wtyczkę do systemu.

### Instalacja automatyczna

1. Użyj wyszukiwarki, żeby odnaleźć wtyczkę Blue Media płatności online.
2. Kliknij Zainstaluj teraz.
3. Zostaniesz przekierowany na stronę z listą zainstalowanych wtyczek.

Po zakończonej instalacji – aktywuj wtyczkę i kliknij Konfiguracja, żeby przejść do panelu konfiguracyjnego wtyczki. 

<img width="602" alt="Widok zainstalowanej wtyczki w menadżerze wtyczek" src="https://user-images.githubusercontent.com/87177993/126705682-bfd8b1ad-c65d-4e54-9a4f-b7fdbfe4fdb0.png">

*Widok zainstalowanej wtyczki w menadżerze wtyczek*

## Konfiguracja

1. Przejdź do zakładki WooCommerce ➝ Ustawienia ➝ Płatności.
2. Wybierz System płatności Blue Media, żeby przejść do konfiguracji.

### Konfiguracja podstawowych pól wtyczki

1.	Przy nazwie System płatności Blue Media ustaw Włącz, dzięki temu Twoi klienci będą mogli już korzystać z płatności internetowych.
2.	W polu Tytuł wpisz nazwę płatności, czyli np.: Płatności Blue Media.
3.	W polu Opis dodaj opis używanej bramki płatności, czyli System płatności Blue Media – Twoi klienci będą widzieć tę nazwę składając zamówienie i wybierając metodę płatności.
4.	W polu Domena Systemu płatności Blue Media uzupełni testowy lub produkcyjny adres URL bramki płatniczej:
- testowy: pay-accept.bm.pl
- produkcyjny pay.bm.pl
	
W momencie, gdy skończysz już sprawdzać, czy wszystko działa prawidłowo – zmień tryb na produkcyjny, wówczas płatności na Twojej stronie będą w 100% aktywne.

5.	Kolejne pola, czyli: Adres do powiadomień (ITN URL) oraz Adres powrotny (Back URL) mają wartość do podglądu – nie masz możliwości wprowadzania zmian. Obydwa adresy musisz przekazać do Blue Media.
6.	Pole Status oczekiwania na płatność określa sposób rezerwacji towaru.
7.	Po uzupełnieniu wszystkich pól – wciśnij: Zapisz zmiany i gotowe.

### Konfiguracja sekcji Ustawienia walut

Wtyczka umożliwia konfigurację sklepu dla walut: PLN, EURO, USD, GBP.

Pola wspólne dla wszystkich walut:

1. Service ID – ma wartość liczbową i jest unikalny dla każdego sklepu (otrzymasz go od Blue Media).
2. Klucz współdzielony – unikalny klucz przypisany do danego sklepu (otrzymasz go od Blue Media).
3. Metoda szyfrowania hash – określa funkcję skrótu, która jest używana do szyfrowania przesyłanych danych. Domyślną funkcją jest SHA256. Funkcja ta jest ustalana na etapie konfiguracji sklepu w systemie płatności BlueMedia. 
4. Płatności w tle:
- ustawiając wartość Tak sprawisz, że klient nie zostaje przekierowany na stronę płatności Blue Media, tylko pozostanie na stronie sklepu, gdzie wyświetlane są dostępne kanały płatności;
- ustawiając wartość Nie aktywujesz przekierowanie na stronę płatności Blue Media;
- przy włączonych Płatnościach w tle użyj przycisku Pobierz/Aktualizuj kanały – żeby pobrać dostępne dla sklepu kanałów płatność;
- możesz również skorzystać z opcji własnego sortowania kanałów płatności – ich kolejność zostanie uwzględniona na stronie zamówienia. 

<img width="602" alt="Przykładowy widok kanałów płatności z włączonym sortowaniem" src="https://user-images.githubusercontent.com/87177993/126706842-51204197-b4b8-4146-a852-f6c54ca04cef.png">

*Przykładowy widok kanałów płatności z włączonym sortowaniem*

Pola dostępne wyłącznie dla waluty PLN:

1. Płatność BLIK PBL – płatność wyświetlana jako osobna pozycja na stronie sklepu, z przekierowaniem do procesora BLIK;
2. Płatność BLIK 0 – płatność wyświetlana jako osobna pozycja na stronie sklepu. Jej wybór spowoduje wyświetlenie się pola tekstowego, do którego należy wprowadzić kod BLIK; 
3. Płatność kartą – płatność wyświetlana jako osobna pozycja na stronie sklepu. Jej wybór spowoduje przekierowanie do serwisu Blue Media, w którym należy podać dane wymagane do płatności kartą. 
4. Płatność ratalna – płatność wyświetlana jako osobna pozycja na stronie sklepu


<img width="482" alt="Przykładowy widok wyboru płatności przy zamówieniu" src="https://user-images.githubusercontent.com/87177993/126707251-450a418d-e3a8-4a60-893a-6c7d804f1c55.png">

*Przykładowy widok wyboru płatności przy zamówieniu*

### Konfiguracja adresów URL
	 	 	 		
Upewnij się, że w panelach administracyjnych Blue Media https://oplacasie.bm.pl oraz https://oplacasie-accept.bm.pl poniższe pola zawierają poprawne adresy sklepu.

●	Konfiguracja adresu powrotu do płatności
https://domena-sklepu.pl/?wc-api=wc_payment_gateway_bluemedia&thank_you_page=1 

●	Konfiguracja adresu, na który jest wysyłany ITN
https://domena-sklepu.pl/?wc-api=wc_payment_gateway_bluemedia 
  
## Logi

1.	Przejdź do zakładki WooCommerce ➝ Status ➝ Logi.

2.	Rozwiń listę w prawym górnym rogu witryny, a znajdziesz tam pliki w formacie bluemedia_payment_gateway-YYYY-MM-DD-hash.log 

Pliki te zawierają logi błędów, które mogą wystąpić podczas procesu płatności. W plikach dostępne są również informacje dotyczące każdej wykonanej płatności za pomocą wtyczki BlueMedia. 

Dane te mogą się okazać przydatne przy zgłaszaniu problemów z działaniem wtyczki. 
