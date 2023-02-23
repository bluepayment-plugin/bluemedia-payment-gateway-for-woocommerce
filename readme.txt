=== Blue Media ===
Contributors: inspirelabs
Tags: woocommerce, bluemedia
Requires at least: 4.1
Tested up to: 6.1
Stable tag: 4.1.15
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

BluePayment to moduł płatności umożliwiający realizację transakcji bezgotówkowych w sklepie opartym na platformie WordPress (WooCommerce).

== Opis ==

BluePayment to moduł płatności umożliwiający realizację transakcji bezgotówkowych w sklepie opartym na platformie WordPress (WooCommerce).

Do najważniejszych funkcji modułu zalicza się:
- realizację płatności online poprzez odpowiednie zbudowanie startu transakcji
- obsługę powiadomień o statusie transakcji (notyfikacje XML)
- obsługę zakupów bez rejestracji w serwisie
- obsługę dwóch trybów działania – testowego i produkcyjnego (dla każdego z nich wymagane są osobne dane kont, po które zwróć się do nas)
- obsługę popularnych metod płatności, które pozwalają Twoim klientom płacić za zakupy w wygodny sposób
- możliwość korzystania z BLIKA.
- wybór banku po stronie sklepu i bezpośrednie przekierowanie do płatności w wybranym banku

Wymagania

- WordPress – przetestowane na wersjach od 4.4 do 6.1
- Wtyczka WooCommerce – przetestowano na wersjach od 4.1 do 6.9.4
- +

== Installation	 ==

Zainstaluj wtyczkę w panelu administracyjnym Wordpress:

1. Pobierz wtyczkę
2. Przejdź do zakładki Wtyczki > Dodaj nową a następnie wskaż pobrany plik instalacyjny.
3. Po zainstalowaniu wtyczki włącz moduł.
1. Przejdź do zakładki WooCommerce ➝ Ustawienia ➝ Płatności.
2. Wybierz Blue Media, żeby przejść do konfiguracji.

Konfiguracja podstawowych pól wtyczki:

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


== Screenshots ==

1. Widok pól do uzupełnienia
2. Dostępne metody płatności

== Changelog ==

= 4.1.8 =
* First release on wp.org

= 4.1.9 =
* Updated documentation

= 4.1.10 =
* Updated documentation
