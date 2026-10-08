# Keso Invest: WordPress tema

Korporativna tema za **KESO INVEST doo**, Novi Sad. Zamenjuje šablonsku temu (Luma Residence / Pavo / Slider Revolution) na https://kesoinvest.com/.

- Klasična PHP tema, bez page buildera, bez Slider Revolution-a i bez dodatnih plugina.
- Zahteva WordPress 6.4+ i PHP 8.1+.
- Jezik: srpski (latinica). Fontovi sadrže č, ć, š, ž, đ.
- Animacije postoje samo na jačim laptop/desktop računarima; sajt je potpuno upotrebljiv i bez njih.

## 1. Instalacija

1. U administraciji: **Izgled → Teme → Dodaj novu → Otpremi temu**, izaberite `keso-invest.zip`, pa **Aktiviraj**.
   (Ili raspakujte folder `keso-invest/` u `wp-content/themes/`.)
2. Pri aktivaciji tema sama:
   - pravi stranice: Početna, Luma Residence, Prvi/Drugi/Treći/Četvrti sprat, Reference, O investitoru, Kontakt, Vesti (i Politiku privatnosti kao **skicu**);
   - postavlja Početnu kao naslovnu i Vesti kao stranicu objava;
   - pravi glavni meni i meni u podnožju (samo ako ih nema);
   - unosi 5 referenci iz tabele sa starog sajta;
   - ako su permalinkovi „obični“, prebacuje ih na `/%postname%/`.
   Ništa postojeće se ne briše i ne prepisuje. Ako stranica sa istim slugom već postoji (npr. `/luma-residence/`), ostaje ista, a tema za nju koristi svoj šablon (stari sadržaj iz Pavo/Elementor šablona se ne prikazuje).
3. Proverite **Podešavanja → Trajni linkovi** (treba da bude „Naziv objave“) i **Podešavanja → Čitanje** (Početna je statična stranica).
4. Tema već koristi priloženi **logotip** (`assets/img/logo-bela.png`, 236×50 px, bela verzija) i prikazuje ga u boji zaglavlja. Ako otpremite svoj logotip (Izgled → Prilagodi → Identitet sajta), on ga zamenjuje. Otpremite i **ikonu sajta**. Preporuka: pravi vektorski logotip (SVG), jer je priložena slika mala.

## 2. Uputstvo za vlasnika

Sve se menja iz administracije, bez koda.

### Kontakt podaci, brojke, Luma Residence, fotografije
**Izgled → Prilagodi → Keso Invest podešavanja**:

| Odeljak | Šta menjate |
|---|---|
| Kontakt podaci | adresa, dva telefona, email, email za poruke sa forme, Facebook/Instagram/LinkedIn (ikonice se prikazuju samo ako je URL unet) |
| Brojke na sajtu | brojke na početnoj; podrazumevano se **zbirovi računaju iz referenci**; polje „Ručno unesi brojke“ ih zaobilazi |
| Luma Residence | lokacija, status, I i II faza, površine stanova i lokala, prekidač „Prikaži cene stanova“ |
| Početna strana i fotografije | naslov i podnaslov početne, glavna fotografija, fotografija projekta, fotografija „O investitoru“, tri slike galerije, slika za deljenje na mrežama (ako ostanu prazne, prikazuje se priložena slika teme) |
| Zaštita kontakt forme | opciono Cloudflare Turnstile ključevi |

### Stanovi
**Stanovi → Dodaj stan.**
1. **Naslov** je oznaka stana (npr. `G-12`).
2. Sa desne strane izaberite **Sprat** (prvi–četvrti) i **Lamelu** (G, A, B).
3. U polju „Podaci o stanu“ unesite tip (stan ili lokal), status (slobodan / rezervisan / prodat), površinu, broj soba, orijentaciju, opciono cenu i šemu (PDF).
4. **Redni broj** određuje redosled na stranici sprata (manji broj ide ranije).
5. Objavite. Stan se odmah pojavljuje na stranici tog sprata, a brojač „slobodnih“ na stranici Luma Residence se ažurira.

Promena statusa: otvorite stan, promenite „Status“, kliknite **Ažuriraj**. U listi stanova postoji filter po statusu.
Cene se prikazuju samo ako je uključeno „Prikaži cene stanova“ i cena je uneta uz stan.

### Reference
**Reference → Dodaj referencu.** Naslov je adresa, a polja su broj stanova i lokala (opciono godina i fotografija kao istaknuta slika). **Zbir** na stranici Reference i na početnoj računa se sam. Redosled određuje polje „Redosled“ (Atributi stranice).

### Vesti
Obične objave (**Objave → Dodaj novu**). Pojavljuju se na `/vesti/`.

### Menjanje menija
**Izgled → Meniji.** Lokacije su „Glavni meni“ i „Meni u podnožju“. Dugme **Kontakt** u zaglavlju je fiksno; ne dodajte Kontakt u glavni meni, da se ne duplira.

### Dodatne stranice
Nova stranica napravljena u uređivaču blokova koristi stilove teme (naslovi, liste, slike, citati, tabele, dugmad, kolone).

## 3. Preusmeravanja (301) i brisanje starih stranica

Stare šablonske stranice se ne prenose. Ako i dalje postoje u administraciji, **možete ih obrisati** (Stranice → Korpa). Ako je neka obrisana, a neko je otvori (npr. iz pretrage), tema je preusmerava sa 301:

| Stara adresa | Nova adresa |
|---|---|
| `/about-us/` | `/o-investitoru/` |
| `/amenities/`, `/apartment/`, `/availability/`, `/building/`, `/gallery/`, `/neighborhood/`, `/residences/` | `/luma-residence/` |
| `/blog/` | `/vesti/` |
| `/faqs/` | `/contact/` |
| `/icons/`, `/sample-page/` | `/` |

Preusmeravanja se aktiviraju **samo kad stranica ne postoji (404)**, da ne prepišu ništa što ste sami napravili. Mapa je u `inc/redirects.php` (filter `keso_redirects`).
Zadržani su postojeći URL-ovi: `/`, `/luma-residence/`, `/prvi-sprat/`, `/drugi-sprat/`, `/treci-sprat/`, `/cetvrti-sprat/`, `/reference/`, `/o-investitoru/`, `/contact/`. Dodato: `/vesti/`.

## 4. Kontakt forma

Radi bez plugina, i bez JavaScripta (običan POST) i sa njim (fetch). Zaštita: nonce, skriveno „honeypot“ polje, potpisan vremenski žig (najmanje 3 s, najviše 48 h), ograničenje na 5 poruka po IP adresi na sat i opciono Cloudflare Turnstile. Poruke grešaka su na srpskom.
Poruka stiže na email iz Customizer-a (podrazumevano `kesoinvestdoo@gmail.com`).
**Važno:** `wp_mail` koristi mail server hostinga. Ako poruke ne stižu, instalirajte SMTP plugin ili podesite SMTP na hostingu i testirajte slanje.
Ograničenje po IP adresi koristi `REMOTE_ADDR`. Iza proksija (npr. Cloudflare) postavite pravi IP na nivou servera.

## 5. Struktura teme

```
keso-invest/
├─ style.css, theme.json, functions.php
├─ inc/        setup, enqueue, cpt, meta, customizer, contact-form, seo, redirects, template-tags, demo (prva aktivacija)
├─ template-parts/
├─ assets/css  critical.css (inline u <head>), main.css (+ .min varijante)
├─ assets/js   core.js (Nivo 0+1), motion.js (Nivo 2), vendor/ (GSAP, ScrollTrigger, Lenis, lokalne kopije)
├─ assets/fonts  Bricolage Grotesque + Hanken Grotesk (OFL, woff2 subset, sadrže č ć š ž đ)
├─ assets/img  logo-bela.png, wordmark.svg, favicon.svg, luma/ (priloženi 3D prikazi)
└─ languages/
```

Animacije po nivoima:
- **Nivo 0:** bez JS-a, sav sadržaj vidljiv; samo CSS prelazi na hover/fokus.
- **Nivo 1** (svi uređaji): otkrivanje pri skrolu, brojači, sticky header (IntersectionObserver, bez scroll listenera).
- **Nivo 2** (samo `≥1024px`, miš, bez „smanji animacije“, bez „štednje podataka“, ≥4 GB RAM-a i ≥4 jezgra): GSAP + ScrollTrigger + Lenis se učitavaju tek tada. Naslovi po linijama, parallax, otkrivanje slika, linije, jedna pinovana sekcija. **Čuvar performansi** meri prvih ~45 frejmova i, ako je prosek > 24 ms, vraća sajt na Nivo 1.

## 6. Pretpostavke

- Boja brenda `#27407D` uzeta je sa znamenja sa starog sajta. Logotip je priložena bela PNG slika (236×50 px); džinovski natpis u podnožju je tekstualni SVG wordmark.
- **Slike:** u temi su priloženi optimizovani **3D prikazi Luma Residence** (`assets/img/luma/`, WebP 480–1600 px + JPG): hero, sekcija Luma Residence, O investitoru, tri slike u galeriji i slika za deljenje na mrežama. To su privremene podrazumevane slike; svaku možete zameniti u Customizer-u (Početna strana i fotografije), pa se priložena više ne prikazuje. Fotografije referenci (zgrade po adresi) nisu dostupne, pa se uz listu referenci prikazuje označeno mesto. Originali su u folderu `slike/` pored teme (nisu deo teme).
- Fontovi: Bricolage Grotesque (naslovi) i Hanken Grotesk (tekst), SIL OFL, self-hosted.
- Stanovi nisu uneti (nema podataka u izvoru). Stranice spratova prikazuju poruku „Pregled uskoro stiže“ dok se stanovi ne dodaju.
- Spratovi su prikazani kao tabela sa filterom po statusu i minimalnoj površini (filter radi uz JavaScript; bez JS-a se vide svi stanovi).
- Tekstovi na sajtu koriste samo činjenice sa starog sajta. Kratke rečenice uz „Vrednosti“ i „Kako gradimo“ su sažeti prepis vrednosti i pristupa sa starog sajta.
- Preusmeravanja starih adresa (tabela iznad) su moj predlog odredišta; promenite ih ako želite drugačije.
- Politika privatnosti je napravljena kao **skica**; tekst mora da unese vlasnik (ZZPL). Dok je skica, link se ne prikazuje.
- Mapa se učitava tek na klik (Google Maps iframe), da ne usporava stranicu.
- Brojke „Lokacije u Novom Sadu“ = broj unetih referenci (5 adresa).

## 7. TODO: vlasnik mora da potvrdi

1. **Brojke stanova i lokala.** Stari sajt protivreči sam sebi: meta opis „130+ stanova i 7 lokala“, tekst „300+ stanova i 10 lokala“, tabela referenci 134 stanova i 7 lokala. Tema koristi **134 / 7 iz tabele** (zbir referenci). Potvrdite i, ako treba, uključite „Ručno unesi brojke“ (Customizer → Brojke na sajtu). U kodu ima `TODO` komentara na odgovarajućim mestima.
2. **Procenat projekata završenih u planiranim rokovima.** Postoji na starom sajtu kao brojač, ali vrednost nije pročitana. Dok polje nije popunjeno, brojka se **ne prikazuje posetiocima**; administrator vidi označenu „TODO“ stavku.
3. **Godine iskustva** podešene na „10+“ (tekst starog sajta: „više od 10 godina“). Potvrdite.
4. **Naselja** (Grbavica, Novo Naselje, Telep) pominju se na starom sajtu bez jasnog konteksta; prikazana su kao red „Naselja: …“ na stranici Reference. Potvrdite ili obrišite (Customizer, prazno polje sakriva red).
5. **Telefon.** Stari sajt je imao i placeholder broj `+381 21 234 56 78`; **izostavljen je**. Korišćeni su `+381 21 30 10 250` i `+381 64 657 9655`.
6. **Društvene mreže.** Stare ikonice vodile su na šablon (Pavo). Izostavljene su; unesite prave URL-ove u Customizer-u.
7. **Pravi logotip** (vektorski, SVG) i **prave fotografije** (stan/zgrade u izgradnji, realizovani objekti, tim). Priloženi su samo 3D prikazi Luma Residence-a; na stranici O investitoru treba fotografija kompanije ili realizovanog objekta, a za reference fotografija svake zgrade.
8. **Stanovi:** unos svih stanova po spratovima (površina, sobe, status, šema).
9. **Politika privatnosti** (tekst) i, ako treba, pravni podaci kompanije u podnožju (PIB, matični broj); nisu u izvoru pa nisu dodati.
10. **SMTP / email:** testirajte stiže li poruka sa forme na `kesoinvestdoo@gmail.com`.
11. **Open Graph slika** (1200×630) za deljenje na mrežama.

## 8. Napomene za programera

- Tema ne zavisi od ACF-a; meta polja su nativna (`register_post_meta` + meta-boxovi). Prefiks funkcija `keso_`, text domain `keso-invest`.
- `theme.json` definiše tokene (boje, fontovi, širine) za editor. Front-end tokeni su CSS promenljive u `assets/css/critical.css` (`:root`).
- Kritični CSS ide inline; `main.css` se učitava bez blokiranja iscrtavanja (`preload` + `noscript`).
- Debug: `SCRIPT_DEBUG` učitava neminifikovane fajlove. Atribut `data-motion` na `<html>` pokazuje stanje animacija (`off`, `loading`, `on`, `degraded`, `failed`); `data-motion-fps-idle` i `data-motion-fps-scroll` beleže izmeren prosek ms po frejmu.
- Biblioteke u `assets/js/vendor/`: GSAP 3.12.7 + ScrollTrigger (GreenSock „Standard License“, besplatno za ovu upotrebu) i Lenis 1.1.20 (MIT).
