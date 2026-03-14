# pickmyanime.com — Cursor Project Spec

## Project Overview

**Domain:** pickmyanime.com  
**Network:** Japan Empire Network (site #10)  
**Stack:** Laravel 11 + Livewire + AlpineJS + TailwindCSS  
**Purpose:** Anime OST & music discovery platform with vinyl culture editorial  
**Monetization:** CDJapan affiliate + Amazon affiliate + cross-traffic to Japan Empire Network  

---

## Core Concept

> "The definitive destination for anime music worth listening to — and owning on vinyl."

Not a streaming service, not a database clone of MAL. This is **editorial-first music discovery** — curated by era, composer, mood, and vinyl availability. The vinyl angle is the differentiator: every OST featured has a section on whether it has been pressed, where to find it, and what to pay.

Target audience: anime fans aged 18–35 who are either already into vinyl or vinyl-curious, crossover with retro JP gaming culture (The725Club audience).

---

## Site Architecture

```
pickmyanime.com/
├── /                          # Homepage — featured OSTs, hero editorial, recent posts
├── /discover                  # Main discovery engine — filterable OST browser
├── /vinyl                     # Vinyl-specific hub — pressings, guides, hunt tips
├── /composers                 # Composer profiles (Kanno, Hisaishi, Sawano, etc.)
├── /eras                      # Era guides (80s City Pop, 90s Golden Age, 2000s, Modern)
├── /shows/{slug}              # Individual anime show page with OST breakdown
├── /ost/{slug}                # Individual OST page with vinyl data + affiliate links
├── /blog                      # Editorial — reviews, vinyl hunting guides, deep dives
├── /blog/{slug}               # Individual post
└── /about                     # Site mission, Japan Empire Network intro
```

---

## Database Schema

### `anime_shows`
```sql
id, title, title_jp, slug, synopsis, year, season (spring/summer/fall/winter),
era_id, genre (json array), cover_image, mal_id (nullable), status,
created_at, updated_at
```

### `composers`
```sql
id, name, name_jp, slug, bio, profile_image, born_year, nationality,
notable_works (json), created_at, updated_at
```

### `soundtracks`
```sql
id, anime_show_id, composer_id, title, title_jp, slug, release_year,
type (ost/single/insert/ed/op), description, cover_image,
has_vinyl (boolean), vinyl_notes, mood_tags (json),
amazon_affiliate_url, cdj_affiliate_url,
featured (boolean), created_at, updated_at
```

### `vinyl_pressings`
```sql
id, soundtrack_id, label, pressing_year, pressing_country (JP/US/EU),
format (LP/EP/7inch/12inch), color_variant, edition_notes,
is_limited (boolean), approximate_price_usd, buy_url (affiliate),
image, created_at, updated_at
```

### `eras`
```sql
id, name, slug, years_range, description, cover_image, sort_order
```

### `articles`
```sql
id, title, slug, body (longtext), excerpt, cover_image,
category (review/guide/editorial/vinyl-hunt), tags (json),
related_soundtrack_id (nullable), related_show_id (nullable),
published_at, created_at, updated_at
```

### `network_links`
```sql
id, site_name, site_url, description, logo, sort_order, active
```

---

## Key Pages

### Homepage `/`
- Hero section: featured OST of the week with full-width visual
- "Discover by mood" — horizontal scroll: Melancholic / Epic / Nostalgic / City Pop / Action / Peaceful
- Featured vinyl picks (3 cards with CDJapan/Amazon affiliate CTAs)
- Latest editorial posts (3)
- Composer spotlight (rotating)
- Japan Empire Network cross-links footer block

### Discover `/discover`
- Filterable grid of soundtracks
- Filters: Era / Composer / Mood / Has Vinyl / Show Genre
- Sort: Recently Added / Year / Alphabetical
- Each card: cover, show name, composer, vinyl badge if available
- Livewire-powered filtering (no page reload)

### OST Detail `/ost/{slug}`
- Hero: cover art, show title, composer, release year, type
- Description & editorial notes
- Tracklist (if available — manual entry)
- **Vinyl section** — all known pressings with:
  - Label, year, country, format, color variant
  - Price range
  - Affiliate CTA: "Find on CDJapan" / "Find on Amazon Japan"
- Mood tags
- Related OSTs (same composer / same era)
- Cross-link: if show has a game → link to The725Club

### Vinyl Hub `/vinyl`
- Editorial intro: "Why anime vinyl?"
- Vinyl hunting guide (blog post featured)
- "Recently pressed" — OSTs that got a new vinyl pressing
- "Holy Grails" — curated list of rare/expensive pressings
- Price tracker notes (manual for now)
- CDJapan affiliate banner

### Composer Profile `/composers/{slug}`
- Bio, portrait
- Full discography on site
- Notable vinyl releases
- Quote or philosophy (where available)
- Related articles

### Era Guides `/eras`
- Cards per era with year range
- `/eras/80s-city-pop` etc. — editorial long-form + OST grid

---

## Affiliate Integration

### CDJapan
- Primary affiliate for vinyl pressings and JP releases
- Affiliate links on: OST pages, vinyl pressings, vinyl hub CTA blocks
- CDJapan has an affiliate program — apply at: https://www.cdjapan.co.jp/aff/
- Link format: standard UTM + affiliate ID appended

### Amazon (Amazon Associates)
- Secondary for Western pressings and turntable equipment
- Entry-level turntable recommendations page (`/vinyl/getting-started`) is a natural affiliate target
- Beginner vinyl setup guide drives equipment affiliate clicks

### Affiliate link storage
- Store in `soundtracks.amazon_affiliate_url` and `soundtracks.cdj_affiliate_url`
- Store in `vinyl_pressings.buy_url`
- All affiliate links wrapped in a helper: `AffiliateLink::track($url, $source)`

---

## Content Seed Plan (Launch MVP)

### Composers (10 at launch)
1. Yoko Kanno (Cowboy Bebop, Ghost in the Shell: SAC, Macross Plus)
2. Joe Hisaishi (Studio Ghibli catalog)
3. Hiroyuki Sawano (Attack on Titan, Kill la Kill)
4. Kenji Kawai (Ghost in the Shell, Fate/Stay Night)
5. Masashi Hamauzu (Final Fantasy XIII — crossover gaming angle)
6. Susumu Hirasawa (Berserk, Paprika, Millennium Actress)
7. Taku Iwasaki (Gurren Lagann, Soul Eater)
8. Yuki Kajiura (Sword Art Online, Madoka Magica, .hack)
9. Michiru Oshima (FMA 2003)
10. Yasunori Mitsuda (Xenogears — gaming crossover)

### OSTs (20 at launch — prioritize vinyl-available)
- Cowboy Bebop OST 1 & 2
- Ghost in the Shell OST
- Nausicaä of the Valley of the Wind
- Spirited Away
- Princess Mononoke
- Akira
- Evangelion OST
- City Hunter OST
- Macross Plus OST
- Attack on Titan Season 1
- Berserk 1997
- Gurren Lagann
- Madoka Magica
- Wolf's Rain
- Trigun
- Escaflowne
- Sailor Moon SuperS
- Dragon Ball Z
- Demon Slayer (Kimetsu no Yaiba)
- One Piece (early arc)

---

## Cross-Network Integration

| Target Site | Link Trigger | Placement |
|---|---|---|
| The725Club | Show has a game adaptation (JP GBA/SNES) | OST page sidebar |
| JapanInPixels | Show set in Japan / real location | Show page |
| JapaneseMythicalCreatures | Show features mythology | Show page + article tags |
| Kohibou | Peaceful/meditative OSTs | Mood filter + articles |
| E2University | Japanese music culture articles | Blog cross-post |

Implement as a `NetworkCrosslink` Blade component — takes `site`, `url`, `reason` props. Renders a subtle "Also on the Japan Empire Network" card.

---

## SEO Strategy

### Target keyword clusters
- `[anime title] vinyl` / `[anime title] soundtrack vinyl`
- `best anime OST vinyl` / `anime vinyl records`
- `[composer name] vinyl` / `[composer] discography`
- `anime city pop vinyl` / `80s anime music`
- `cowboy bebop vinyl` (high volume, high intent)

### Technical SEO
- SSR via Laravel Blade — full SEO-friendly rendering
- `<title>`: `{OST Title} — Anime Vinyl & OST Guide | PickMyAnime`
- Open Graph images: auto-generated per OST using cover art
- Schema markup: `MusicAlbum` on OST pages, `Person` on composer pages
- Canonical URLs on all filtered discovery pages
- Sitemap auto-generated via `spatie/laravel-sitemap`
- Internal linking: every OST → composer → era → related OSTs

### Content velocity
- 2 editorial articles/week at launch
- 1 vinyl pressing spotlight/week
- OST database grows incrementally — don't need to launch with 500 entries

---

## Admin Panel

Use Laravel Filament (already standard in network).

Resources:
- `AnimeShowResource`
- `ComposerResource`
- `SoundtrackResource` (with vinyl pressings as a repeater)
- `VinylPressingResource`
- `ArticleResource` (with rich text editor)
- `EraResource`
- `NetworkLinkResource`

---

## Laravel Project Setup

```bash
laravel new pickmyanime
cd pickmyanime

# Core packages
composer require livewire/livewire
composer require filament/filament
composer require spatie/laravel-sitemap
composer require spatie/laravel-sluggable
composer require spatie/laravel-tags

# Frontend
npm install alpinejs
npm install -D tailwindcss @tailwindcss/typography
```

### Environment
```env
APP_NAME=PickMyAnime
APP_URL=https://pickmyanime.com
DB_DATABASE=pickmyanime
```

---

## Visual Design Direction

- **Palette:** Deep navy / midnight blue base + warm amber/gold accent (vinyl label colors) + off-white text
- **Typography:** Clean sans-serif for UI, slightly editorial serif for article body
- **Aesthetic:** Late-night listening session — moody, cinematic, not kawaii/bright
- **Cover art** is the hero element — large imagery throughout, let the album art breathe
- **Vinyl record SVG motif** as decorative element in headers
- Reference: Pitchfork meets Japanese editorial design meets retro LP sleeve art
- NOT anime merch store aesthetic — this is a serious music editorial site

---

## Phase Plan

### Phase 1 — Foundation (Week 1-2)
- [ ] Laravel install + DB migrations
- [ ] Filament admin setup
- [ ] Seed: 5 composers, 10 OSTs, 5 vinyl pressings
- [ ] Homepage + Discover page (no filters yet)
- [ ] Basic OST detail page

### Phase 2 — Discovery Engine (Week 3)
- [ ] Livewire filter system on /discover
- [ ] Vinyl pressing section on OST pages
- [ ] Affiliate links live (CDJapan + Amazon)
- [ ] Composer profile pages
- [ ] Era pages

### Phase 3 — Editorial (Week 4)
- [ ] Blog/articles system
- [ ] 5 seed articles (vinyl guides + OST deep dives)
- [ ] SEO meta layer
- [ ] Sitemap + robots.txt
- [ ] NetworkCrosslink component live

### Phase 4 — Polish & Launch
- [ ] Mobile responsive pass
- [ ] PageSpeed optimization (target 90+)
- [ ] CDJapan affiliate application submitted
- [ ] Amazon Associates setup
- [ ] Submit to Google Search Console
- [ ] Cross-link from Japan Empire Network sites

---

## File Structure Notes

```
resources/views/
├── layouts/app.blade.php
├── components/
│   ├── ost-card.blade.php
│   ├── vinyl-pressing.blade.php
│   ├── composer-card.blade.php
│   ├── network-crosslink.blade.php
│   └── affiliate-cta.blade.php
├── pages/
│   ├── home.blade.php
│   ├── discover.blade.php
│   ├── vinyl/index.blade.php
│   ├── composers/index.blade.php
│   ├── composers/show.blade.php
│   ├── shows/show.blade.php
│   ├── ost/show.blade.php
│   ├── eras/index.blade.php
│   ├── blog/index.blade.php
│   └── blog/show.blade.php
```

---

*Spec version 1.0 — March 2026*  
*Part of the Japan Empire Network*
