[System płatności online Blue Media](https://platnosci.bm.pl/) dla WooCommerce
================================================================

#### Instalacja

##### Instalacja ręczna

1. Sciągnij ze [strony](https://github.com/bluepayment-plugin/bluemedia-payment-gateway-for-woocommerce/archive/v1.1.0.zip) wtyczkę dla WooCommerce.
2. Przejdź do panelu administracyjnego i otwórz zakładkę "Wtyczki". Kliknij "Dodaj nową", następnie wybierz "Wyślij wtyczkę na serwer".
3. Wybierz plik który wcześniej został ściągnięty, następnie kliknij "Zainstaluj". Po instalacji kliknij "Włącz wtyczkę".
4. Przy wtyczce "System płatności online Blue Media dla WooCommerce" Kliknij "Konfiguracja".
5. Teraz należy dokonać odpowiednich ustawień dla modułu płatności "System płatności online Blue Media":
    a. "Włącz/Wyłącz" – należy pozostawić zaznaczone, aby klienci mogli dokonywać płatności przez "System płatności online Blue Media".
    b. "Tytuł" – nazwa płatności 
    c. "Opis" - opis bramki "System płatności online Blue Media", który widzi użytkownik przy tworzeniu zamówienia
    d. "Tryb działania" – przełączanie się między środowiskiem testowym a produkcyjnym "Systemu płatności online Blue Media", jeśli testy zakończyły się, należy ustawić wartość "produkcyjny".
    e. "Domena Systemu płatności online Blue Media" - wartość tylko do podglądu, wartość w tym polu nie jest możliwa do zmiany.
    f. "Adres do powiadomień (ITN URL)" - wartość tylko do podglądu, wartość w tym polu nie jest możliwa do zmiany - adres ten należy przekazać "Blue Media".
    g. "Adres powrotny (Back URL)" - wartość tylko do podglądu, wartość w tym polu nie jest możliwa do zmiany - adres ten należy przekazać "Blue Media".
    h. "ServiceID" - pole obowiązkowe, ID usługi otrzymane od "Blue Media".
    i. "Hash key" - pole obowiązkowe, klucz do hashowania danych otrzymane od "Blue Media".
4. Następnie należy kliknąć "Zapisz zmiany".

#### Licencja
Biblioteka ta jest na licencji licencji GPL-3.0. Proszę zobaczyć [LICENSE](LICENSE.md) po więcej szczegółów.

#### Historia zmian
Proszę zobaczyć [CHANGELOG](CHANGELOG.md) po więcej szczegółów.

#### Wymaganie
Ten dodatek został przetestowny i działa poprawnie na wersjach:
Wordpress: 4.4 - 4.8
Woocommerce: 2.1 - 3.1

Wymagane rozszerzenia PHP
- xmlwriter
- xmlreader


Najniższa wymagana wersja WooCommerce do działania wtyczki to wersja 2.1.

#### Kontakt i wsparcie
W razie jakichkolwiek problemów technicznych proszę o kontakt z [Blue Media](info@bluemedia.pl).
