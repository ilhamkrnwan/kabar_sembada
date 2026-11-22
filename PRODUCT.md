# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

WordPress block theme (`theme.json` v3) + companion plugin. The plugin owns the
custom blocks (`kabarsembada/*`), post views, SEO, search REST and the GSAP
motion layer. Build with `@wordpress/scripts` via Bun. Local dev on Docker
(`compose.yml`).

## Users

Residents of Kabupaten Sleman (DIY) and people who work, study, or travel
there, reading on phone first: what the regency government decided, what
affects their kapanewon today (roads, weather, Merapi, prices, school
admission, public services), then one full story.

## Product Purpose

Production site for "Kabar Sembada" (kabarsembada.com), rebuilt from the old
Elementor site. Design follows theme-1 of the prototype at
`C:\KAIRAV\portal_project\theme-1` ("Suprast" news layout).

## Positioning

Local first: news and service information about Sleman's 17 kapanewon and 86
kalurahan, mixing reporting (pemerintahan, peristiwa, ekonomi, pendidikan,
kesehatan, wisata) with practical daily info.

## Capabilities and Constraints

- Every component is a block an editor can change in place.
- Editorial curation uses native WordPress: sticky posts (Pilihan Redaksi),
  tags (Fokus Berita), categories (nav, chips).
- Most-read lists come from the plugin's own views counter.
- Comments use core blocks, open on posts, closed on pages.
- Honest content: empty states hide themselves; no fake UI (no newsletter until
  a provider exists).
- Old URLs (`/%postname%/`) must keep working after the import.

## Brand Commitments

- Name: Kabar Sembada.
- Brand color: `oklch(74.7% 0.1517 235.8)` (sky blue). Text on it is dark navy;
  links on white use a darker shade of the same hue.
- Font: Manrope 500–800.
- Language: Bahasa Indonesia for all UI strings.

## Product Principles

1. Today's local info is readable at a glance.
2. Search finds a story in two keystrokes.
3. The article page is built for reading: comfortable measure, clear hierarchy.
4. Fast on a mid-range phone: CSS first, GSAP only where it adds meaning, and
   always respects `prefers-reduced-motion`.
