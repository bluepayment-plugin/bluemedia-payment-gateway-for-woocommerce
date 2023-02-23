# Instrukcja modułu BluePayment dla platformy WooCommerce

## Podstawowe informacje

BluePayment to moduł płatności umożliwiający realizację transakcji bezgotówkowych w sklepie opartym na platformie WordPress (WooCommerce). Jeżeli jeszcze nie masz wtyczki, możesz ją pobrać [tutaj.](https://pl.wordpress.org/plugins/platnosci-online-blue-media/)


![Instrukcja pobierania archiwum z wtyczką](/assets/img/screenshot_11.png)
<em>Instrukcja pobierania archiwum z wtyczką<em>


### Główne funkcje

Do najważniejszych funkcji modułu zalicza się:
- realizację płatności online poprzez odpowiednie zbudowanie startu transakcji
- obsługę powiadomień o statusie transakcji (notyfikacje XML)
- obsługę zakupów bez rejestracji w serwisie
- obsługę dwóch trybów działania – testowego i produkcyjnego (dla każdego z nich wymagane są osobne dane kont, po które zwróć się do nas)
- obsługę popularnych metod płatności, które pozwalają Twoim klientom płacić za zakupy w wygodny sposób
- możliwość korzystania z BLIKA
- wybór banku po stronie sklepu i bezpośrednie przekierowanie do płatności w wybranym banku

### Wymagania

- WordPress – przetestowane na wersjach od 4.4 do 6.1.1
- Wtyczka WooCommerce – przetestowano na wersjach od 4.1 do 7.3.0
- PHP minimum 7.2

### Instalacja wtyczki z katalogu wtyczek Wordpressa (zalecane)
1. Zaloguj się do swojego panelu administracyjnego w serwisie WordPress – używając loginu i hasła.
2. W menu po lewej stronie wybierz Wtyczki ➝ Dodaj nową.
3. Wpisz "Blue Media" w pole wyszukiwania. Powinieneś zobaczyć wtyczkę Blue Media jako jedną z pierwszych (jak na zrzucie ekranu).
4. Kliknij "zainstaluj" a następnie "włącz"

![Wyszukiwanie wtyczki w katalogu](/assets/img/screenshot_10.png)

### Instalacja wtyczki z archiwum zip

1. Przejdź na stronę wtyczki: https://pl.wordpress.org/plugins/platnosci-online-blue-media/
2. Kliknij "Pobierz".
3. Zaloguj się do swojego panelu administracyjnego w serwisie WordPress – używając loginu i hasła.
4. W menu po lewej stronie wybierz Wtyczki ➝ Dodaj nową.
5. Kliknij "Wyślij wtyczkę na serwer".
6. Wybierz pobrany w punkcie 2 plik z wtyczką z rozszerzeniem .zip
7. Kliknij "Zainstaluj", żeby wgrać wtyczkę do systemu.
8. Po instalacji klikjnij "Włącz"


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

7.	W polu “Testowy Klucz konfiguracyjny (hash)” wpisz Twój testowy Klucz konfiguracyjny (hash).
      W polu “Identyfikator serwisu” wpisz identyfikator serwisu.
8.	W polu “Klucz konfiguracyjny (hash)” wpisz Klucz konfiguracyjny (hash).

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
{URL Twojego sklepu}/?bm_gateway_return

Przykład: https://mój-sklep.com/?bm_gateway_return

●	Konfiguracja adresu, na który jest wysyłany ITN
{URL Twojego sklepu}/?wc-api=wc_gateway_bluemedia

Przykład: https://mój-sklep.com/?wc-api=wc_gateway_bluemedia

### Rozszerzona Analityka Google Analytics 4

Dzięki temu rozwiązaniu, możesz dokładniej analizować ścieżkę zakupową Twoich klientów.

Po dokonaniu poniższej konfiguracji, Google Analytics zacznie zbierać szczegółowe dane na temat zachowań użytkowników

Moduł dodaje obsługę dodatkowych zdarzeń w Google Analytics 4:
- view_item_list – użytkownik zobaczył produkt na liście,
- view_item – użytkownik wyświetlił stronę produktu,
- add_to_cart – użytkownik dodał produkt do koszyka,
- remove_from_cart – użytkownik usunął produkt z koszyka,
- begin_checkout – użytkownik rozpoczął proces zamówienia (koszyk i/lub wybór metody dostawy),
- checkout_progress – użytkownik przeszedł do drugiego kroku zamówienia (wybór metody płatności),
- set_checkout_option – użytkownik uzupełnił dane zamówienia,
- purchase – użytkownik złożył zamówienie oraz opłacił je poprzez płatność BlueMedia (oznaczenie jako konwersja).

### Konfiguracja Google Analytics

Wpisz Klucz śledzący Google Analytics oraz pozostałe dane GA4 we wskazane pola i zapisz zmiany.

![Konfiguracja Google Analytics](/assets/img/screenshot_6.jpg)

### Statusy płatności

Nowa funkcja wyboru i dopasowania statusów płatności pozwala na automatyczną zmianę statusu zamówienia Woocoemmerce w momencie zmiany statusu płatności.

![Konfiguracja statusów](/assets/img/screenshot_4.jpg)


### Najczęściej zadawane pytania


#### Czy można włączać i wyłączać metody płatności?
Niestety, w przypadku Płatności dla WooCommerce nie ma takiej możliwości. Wszystkie dostępne metody płatności są automatycznie włączone.


#### Jak włączyć BLIK 0?
BLIK 0 nie jest dostępną metodą płatności w Płatnościach dla WooCommerce. Dostępny jest natomiast BLIK z przekierowaniem na serwis blik.pl (w modelu white label) oraz szereg innych metod płatności, chętnie wybieranych przez klientów.


#### Czy jest możliwość "wyciągnięcia" samego BLIK-a na white label?
Niestety nie ma takiej możliwości.

#### Czy można dodać inną walutę?
Tak, obsługiwane waluty to: PLN, EUR, CZK, RON, HUF
#### Jak zlecać zwroty (z poziomu sklepu czy panelu PayBM)?
Aktualnie zwroty należy zlecać z poziomu panelu administracyjnego PayBM. Zaloguj się do oplacasie.bm.pl i wejdź w zakładkę 'Transakcje', po czym:

- kliknij 'strzałkę zwrotu' (⟲);
- zaznacz płatności, które chcesz zwrócić;
- kliknij 'zwróć zaznaczone';
- uzupełnij dane do zwrotu;
- kliknij 'Zwróć', żeby potwierdzić akcję.



Nie znalazłeś odpowiedzi na swoje pytanie? [Sprawdź naszą bazę wiedzy](https://developers.bluemedia.pl/online/faq), gdzie zebraliśmy wszystkie pytania dotyczące naszych usług.
