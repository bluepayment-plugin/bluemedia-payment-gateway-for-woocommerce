# Instrukcja modułu BluePayment dla platformy WooCommerce

## Podstawowe informacje

BluePayment to moduł płatności umożliwiający realizację transakcji bezgotówkowych w sklepie opartym na platformie WordPress (WooCommerce). Jeżeli jeszcze nie masz wtyczki, możesz ją pobrać [tutaj.](https://github.com/bluepayment-plugin/bluemedia-payment-gateway-for-woocommerce/releases)

Należy zawsze pobierać najnowsze wydanie oznaczone etykietą "latest" (plik z ikonką paczki, zobacz poniższy screen).

![Instrukcja pobierania archiwum z wtyczką](/assets/img/screenshot_3.png)
<em>Instrukcja pobierania archiwum z wtyczką<em>


### Główne funkcje

Do najważniejszych funkcji modułu zalicza się:
- realizację płatności online poprzez odpowiednie zbudowanie startu transakcji
- obsługę powiadomień o statusie transakcji (notyfikacje XML)
- obsługę zakupów bez rejestracji w serwisie
- obsługę dwóch trybów działania – testowego i produkcyjnego (dla każdego z nich wymagane są osobne dane kont, po które zwróć się do nas)
- obsługę popularnych metod płatności, które pozwalają Twoim klientom płacić za zakupy w wygodny sposób
- możliwość korzystania z BLIKA na dwa sposoby (z przekierowaniem na stronę eblik.pl lub jako BLIK 0)
- wybór banku po stronie sklepu i bezpośrednie przekierowanie do płatności w wybranym banku

### Wymagania

- WordPress – przetestowane na wersjach od 4.4 do 6.0.2
- Wtyczka WooCommerce – przetestowano na wersjach od 4.1 do 6.9.4
- PHP minimum 7.2 - wersja zalecana przez Woocommerce

## Instalacja modułu
1. Pobierz wtyczkę.
2. Zaloguj się do swojego panelu administracyjnego w serwisie WordPress – używając loginu i hasła.
3. W menu po lewej stronie wybierz Wtyczki ➝ Dodaj nową.
4. Zainstaluj wtyczkę wybierając jedną z poniższych metod.

### Instalacja wtyczki

1. Kliknij Wyślij wtyczkę na serwer.
2. Wybierz plik z wtyczką z rozszerzeniem .zip
3. Kliknij Zainstaluj teraz, żeby wgrać wtyczkę do systemu.

## Konfiguracja

1. Przejdź do zakładki WooCommerce ➝ Ustawienia ➝ Płatności.
2. Wybierz Blue Media, żeby przejść do konfiguracji.

### Konfiguracja podstawowych pól wtyczki

1.	Przy nazwie Blue Media ustaw Włącz, dzięki temu Twoi klienci będą mogli już korzystać z płatności internetowych.
2.	Zaznacz pole: “Pokazuj metody płatności w sklepie”
3.	W polu Nazwa modułu płatności w sklepie wpisz nazwę płatności, czyli np.: Płatności Blue Media.

4.	W polu Opis modułu płatności w sklepie dodaj opis używanej bramki płatności, czyli Blue Media – Twoi klienci będą widzieć tę nazwę składając zamówienie i wybierając metodę płatności.
5.	W polu “tryb działania” Zaznacz “włącz tryb testowy” aby włączyć tryb sandbox.

6.	W polu “Testowy identyfikator serwisu” wpisz Twój testowy identyfikator serwisu.

7.	W polu “Testowy klucz współdzielony” wpisz Twój testowy klucz współdzielony.
      W polu “Identyfikator serwisu” wpisz identyfikator serwisu.
8.	W polu “Klucz współdzielony” wpisz klucz współdzielony.

Powyższe pola uzupełnisz danymi, które otrzymasz od Blue Media S.A. Jeśli jeszcze ich nie masz - skontaktuj się z nami.
W momencie, gdy skończysz już sprawdzać, czy wszystko działa prawidłowo – wyłącz tryb testowy, wówczas płatności na Twojej stronie będą w 100% aktywne.
Po uzupełnieniu wszystkich pól – wybierz: Zapisz zmiany i gotowe.

![Widok pól do uzupełnienia](/assets/img/screenshot_1.png)
<em>Widok pól do uzupełnienia<em>


![Dostępne metody płatności](/assets/img/screenshot_2.png)

<em>Dostępne metody płatności</em>

### Konfiguracja adresów URL

Upewnij się, że w panelach administracyjnych Blue Media https://oplacasie.bm.pl oraz https://oplacasie-accept.bm.pl poniższe pola zawierają poprawne adresy sklepu.

●	Konfiguracja adresu powrotu do płatności
https://domena-sklepu.pl/?bm_gateway_return

●	Konfiguracja adresu, na który jest wysyłany ITN
https://domena-sklepu.pl/?wc-api=wc_gateway_bluemedia

## Logi

1.	Przejdź do zakładki WooCommerce ➝ Status ➝ Logi.

2.	Rozwiń listę w prawym górnym rogu witryny, a znajdziesz tam pliki w formacie bluemedia_payment_gateway-YYYY-MM-DD-hash.log

Pliki te zawierają logi błędów, które mogą wystąpić podczas procesu płatności. W plikach dostępne są również informacje dotyczące każdej wykonanej płatności za pomocą wtyczki BlueMedia.

Dane te mogą się okazać przydatne przy zgłaszaniu problemów z działaniem wtyczki. 
