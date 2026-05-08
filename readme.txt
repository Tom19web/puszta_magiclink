=== PusztaPlay Auth + CRM Plugin ===
Contributors: PusztaPlay
Tags: magic login, passwordless, security, crm, tv, iptv, xtream, qr, profiles
Requires at least: 5.8
Tested up to: 6.5
Stable tag: 2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Biztonságos jelszó nélküli bejelentkezés, QR TV auth, előfizetés-kezelő CRM, profil szinkronizáció, emlékeztetők és számlázás. Végre golyóálló, elegáns és moduláris formában!

== Description ==

Felejtsd el a jelszavakat! A PusztaPlay Auth + CRM elhozza a kényelem és a kíméletlen biztonság fúzióját. Elegáns magic-link belépés, QR kódos TV auth, profi ügyfélkezelő CRM és FireTV app profil szinkronizáció — minden egy helyen.

= Funkciók =
* **Jelszó nélküli belépés:** E-mailben küldött magic link
* **QR kódos TV bejelentkezés:** REST API végpontok a PusztaPlayer FireTV app-hoz
* **CRM:** Ügyfélkód, csomag, lejárat, státusz nyilvántartás
* **Xtream API integráció:** Szerveroldali account info (regisztráció, lejárat, kapcsolatok, trial státusz) — 5 perces cache
* **Profil szinkronizáció:** Netflix-stílusú profilok a FireTV app-hoz (REST API: létrehozás, mentés, törlés)
* **Dashboard profilkezelés:** Profilok törlése, kedvencek/megnézendők törlése a WP felületről
* **Golyóálló biztonság:** Hashelt tokenek, rate-limiting, IP brute-force védelem
* **Admin felület:** Felhasználói lista extra oszlopokkal, profil extra mezők, új tag felvétele
* **SMTP konfiguráció:** Egyedi email kiszolgáló a magic link-ekhez

= Shortcode-ok =
* `[pusztaplay_login]`: Bejelentkező űrlap
* `[pusztaplay_dashboard]`: Vezérlőpult — fiók adatok + előfizetési státusz
* `[pusztaplay_service_info]`: **(ÚJ)** Xtream szerveroldali infók (regisztráció, lejárat, kapcsolatok, formátumok)
* `[pusztaplay_profile_manager]`: **(ÚJ)** Profilkezelő — profil törlés, kedvencek/megnézendők törlése
* `[pusztaplay_header_btn]`: Dinamikus gomb a fejlécbe
* `[pusztaplay_logout_btn]`: Kijelentkezés gomb
* `[pusztaplay_vedett] Tartalom [/pusztaplay_vedett]`: Prémium tartalomzár

= REST API végpontok (PusztaPlayer FireTV app-hoz) =
* `POST /pusztaplay/v1/qr-request`: QR kód generálás
* `GET /pusztaplay/v1/qr-poll?code=X`: Bejelentkezés állapota
* `GET /pusztaplay/v1/profiles?api_key=X`: Profilok lekérése
* `POST /pusztaplay/v1/profiles?api_key=X`: Profilok mentése
* `POST /pusztaplay/v1/profile?api_key=X`: Egyedi profil (create/save/delete)

== Installation ==

1. Töltsd fel a `pusztaplay-magic-login` mappát a `/wp-content/plugins/` könyvtárba
2. Aktiváld a bővítményt (automatikusan létrejön a 'Belépés' és 'Vezérlőpult' oldal)
3. Navigálj a `Beállítások -> Magic Login` menübe
4. Töltsd ki az SMTP adatokat (Host, Port, User, Pass) és az előfizetői csomagokat
5. Kész!

== Changelog ==

= 2.1 =
* ÚJ: Plugin átnevezve "PusztaPlay Auth + CRM Plugin"-ra
* ÚJ: `[pusztaplay_service_info]` shortcode — Xtream szerveroldali account info (player_api.php, 5p cache)
* ÚJ: `[pusztaplay_profile_manager]` shortcode — profil törlés, kedvencek/megnézendők törlése a dashboardról
* ÚJ: Admin-AJAX handlerek profilkezeléshez (nonce + user auth védelemmel)
* ÚJ: Frontend JS + CSS a profilkezelő interakciókhoz (fetch API, confirm dialógus, animációk)
* ÚJ: `PP_XTREAM_SERVER` konstans (`https://live.pusztaplay.eu`)
* ÚJ: `pp_fetch_xtream_account_info()` segédfüggvény (5 perces transient cache)
* FRISSÍTVE: 'Vezérlőpult' oldal automatikusan tartalmazza az új shortcode-okat

= 2.0 =
* TELJES ÚJRAÍRÁS: Moduláris architektúra
* BIZTONSÁGI FRISSÍTÉS: Hashelt tokenek, Nonce ellenőrzés
* DIZÁJN: Frontend és admin UI külön templates / assets mappákban
* ÚJ: QR kódos TV bejelentkezés REST API-val
* ÚJ: Profil szinkronizáció REST API
* ÚJ: Mini CRM (ügyfélkód, csomag, lejárat)

= 1.3 =
* A "golyóálló verzió", ami valójában egy procedurális, egyfájlos rémálom volt. Sosem beszélünk róla többé.
