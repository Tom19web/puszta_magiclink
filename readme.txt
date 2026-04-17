=== PusztaPlay Magic Login ===
Contributors: PusztaPlay
Tags: magic login, passwordless, security, login, puszta
Requires at least: 5.8
Tested up to: 6.5
Stable tag: 2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Biztonságos bejelentkezés jelszó nélkül, e-mailben küldött egyszer használatos linkkel. Végre golyóálló, elegáns és moduláris formában!

== Description ==

Felejtsd el a jelszavakat és a sebezhető bejelentkezési űrlapokat! A PusztaPlay Magic Login elhozza neked a kényelem és a kíméletlen biztonság tökéletes fúzióját. Egy elegáns, egyszer használatos varázs-linkkel engedjük be a kiváltságosokat, miközben a pórnépet kizárjuk.

= Fenséges Funkciók =
* **Jelszó nélküli belépés:** Csak egy e-mail cím, és a mágia máris a felhasználó postaládájába repül.
* **Golyóálló biztonság:** Hashelt tokenek, szigorú rate-limiting, IP alapú bruteforce védelem és linkszkenner-verő megerősítő oldal.
* **Mini CRM a színfalak mögött:** Tartsd nyilván alattvalóid ügyfélkódját, előfizetési csomagját és annak lejárati idejét egyenesen a felhasználói profiljukon!
* **Diktatórikus Admin Tisztogatás:** Eltünteti a WordPress idegesítő, felesleges profilmezőit és a harmadik féltől származó bővítmények (Elementor, Yoast) tolakodó dobozait. Csak a tiszta rend marad.
* **Tökéletes Moduláris Architektúra:** Nincs több spagettikód. Sablonok, stíluslapok és tiszta objektumorientált logika a motorháztető alatt.

= Használható Shortcode-ok =
* `[pusztaplay_login]`: A pop-art stílusú, harsány bejelentkező űrlap (és a sikeres belépés kártyája).
* `[pusztaplay_dashboard]`: Az elegáns vezérlőpult, ahol az ügyfelek szembesülnek előfizetésük nyers valóságával.
* `[pusztaplay_header_btn]`: Dinamikus gomb a fejlécbe (Bejelentkezés / Tovább a Dashboardra).
* `[pusztaplay_logout_btn]`: Azonnali kijelentkezés gomb a menekülni vágyóknak.
* `[pusztaplay_vedett] Titkos tartalom [/pusztaplay_vedett]`: Prémium tartalomzár, ami elrejti a lényeget a fizetés nélküli halandók elől.

== Installation ==

Az arisztokrácia is követi a szabályokat. Így telepítsd a bővítményt:

1. Töltsd fel a `pusztaplay-magic-login` mappát a `/wp-content/plugins/` könyvtárba.
2. Aktiváld a bővítményt a WordPress 'Bővítmények' menüpontjában. (A rendszer automatikusan létrehozza a 'Belépés' és 'Vezérlőpult' oldalakat).
3. Navigálj a `Beállítások -> Magic Login` menüpontba.
4. Töltsd ki a fenséges SMTP adataidat (Host, Port, User, Pass) és add meg az előfizetői csomagjaidat vesszővel elválasztva.
5. Dőlj hátra, és élvezd a hatalmat!

== Frequently Asked Questions ==

= Miért nem kapom meg az e-mailt? =
Valószínűleg hanyagul töltötted ki az SMTP beállításokat a `Beállítások -> Magic Login` menüben, vagy a szervered gyenge ahhoz, hogy kiküldje. Ellenőrizd a beállításokat, te kis kókler!

= Hogyan tilthatom ki a halandókat a wp-adminból? =
A bővítmény alapértelmezetten eltünteti az Admin Bar-t az egyszerű előfizetők elől, és a hagyományos `wp-login.php` oldalt is kíméletlenül átirányítja a saját, gyönyörű bejelentkező oldalunkra. A kontroll a te kezedben van.

= Be tudom állítani, hogy meddig éljen a link? =
A kód mélyén a link élettartama szigorúan 15 percbe van betonozva. Aki ennyi idő alatt nem képes rákattintani egy gombra, az nem érdemli meg a PusztaPlay tartalmait.

== Changelog ==

= 2.0 =
* TELJES ÚJRAÍRÁS: A spagettikód korszaka lezárult. Bevezettük az elegáns, moduláris architektúrát (MVC modell).
* BIZTONSÁGI FRISSÍTÉS: A tokenek immár hashelve kerülnek az adatbázisba (Tranziensként).
* ÚJ FUNKCIÓ: Szigorú Nonce ellenőrzés a formoknál a CSRF támadások ellen.
* DIZÁJN: A frontend és admin UI elemek külön `templates` és `assets` mappákba lettek száműzve.
* TISZTOGATÁS: Admin felület kíméletlen sterilizálása az idegen pluginok szemetétől.

= 1.3 =
* Az úgynevezett "golyóálló verzió", ami valójában egy procedurális, egyfájlos rémálom volt. Sosem beszélünk róla többé. De legalább volt benne egy űrrakéta animáció.