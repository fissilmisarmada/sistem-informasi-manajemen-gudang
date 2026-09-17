---
name: "Sistem Informasi Manajemen Gudang"
description: "Andon lantai gudang — parchment, ink, dan marker-amber presisi untuk scan cepat di lorong rak."
colors:
  amber: "#F7D60A"
  amber-dim: "#E6C200"
  amber-glow: "rgba(247,214,10,0.14)"
  ink: "#0F172A"
  navy: "#0F2540"
  navy-deep: "#162E52"
  bg: "#F6F1E8"
  surface: "#FFFFFF"
  surface-soft: "#F8FAFC"
  surface-muted: "#EEF2F7"
  line: "#E2E8F0"
  line-strong: "#CBD5E1"
  muted: "#475569"
  faint: "#94A3B8"
  red: "#DC2626"
  red-surface: "#FEF2F2"
  green: "#16A34A"
  green-surface: "#ECFDF5"
  saas-indigo: "#4F46E5"
  saas-indigo-surface: "#EEF2FF"
  saas-indigo-border: "#C7D2FE"
  saas-teal: "#0D9488"
  saas-teal-surface: "#CCFBF1"
  saas-teal-border: "#99F6E4"
typography:
  display:
    fontFamily: "'Instrument Sans', ui-sans-serif, system-ui, sans-serif"
    fontSize: "clamp(1.375rem, 4vw, 1.75rem)"
    fontWeight: 800
    lineHeight: 0.95
    letterSpacing: "-0.04em"
  headline:
    fontFamily: "'Instrument Sans', ui-sans-serif, system-ui, sans-serif"
    fontSize: "22px"
    fontWeight: 800
    lineHeight: 1.1
    letterSpacing: "-0.03em"
  title:
    fontFamily: "'Instrument Sans', ui-sans-serif, system-ui, sans-serif"
    fontSize: "13px"
    fontWeight: 700
    lineHeight: 1.3
    letterSpacing: "-0.02em"
  body:
    fontFamily: "'Instrument Sans', ui-sans-serif, system-ui, sans-serif"
    fontSize: "14px"
    fontWeight: 500
    lineHeight: 1.5
    letterSpacing: "-0.01em"
  label:
    fontFamily: "'Instrument Sans', ui-sans-serif, system-ui, sans-serif"
    fontSize: "11px"
    fontWeight: 800
    lineHeight: 1.2
    letterSpacing: "0.10em"
  mono:
    fontFamily: "'JetBrains Mono', ui-monospace, monospace"
    fontSize: "12px"
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "0.02em"
rounded:
  sm: "12px"
  md: "16px"
  lg: "18px"
  xl: "20px"
  pill: "999px"
spacing:
  xs: "6px"
  sm: "10px"
  md: "16px"
  lg: "20px"
  xl: "24px"
components:
  button-primary:
    backgroundColor: "{colors.ink}"
    textColor: "{colors.surface}"
    rounded: "{rounded.sm}"
    padding: "0 16px"
    height: "38px"
  button-primary-hover:
    backgroundColor: "{colors.navy-deep}"
    textColor: "{colors.surface}"
    rounded: "{rounded.sm}"
  button-amber:
    backgroundColor: "{colors.amber}"
    textColor: "{colors.ink}"
    rounded: "{rounded.sm}"
    padding: "0 16px"
    height: "38px"
  button-ghost:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
    rounded: "{rounded.sm}"
    padding: "0 16px"
    height: "38px"
  card:
    backgroundColor: "{colors.surface}"
    rounded: "{rounded.xl}"
    padding: "16px"
  input:
    backgroundColor: "#FBFBFD"
    textColor: "{colors.ink}"
    rounded: "14px"
    padding: "11px 14px"
  badge-green:
    backgroundColor: "{colors.green-surface}"
    textColor: "#065F46"
    rounded: "{rounded.pill}"
    padding: "3px 9px"
  badge-red:
    backgroundColor: "{colors.red-surface}"
    textColor: "#991B1B"
    rounded: "{rounded.pill}"
    padding: "3px 9px"
---

# Design System: Sistem Informasi Manajemen Gudang

## Overview

**Creative North Star: "Andon Lantai Gudang"**

Sistem ini adalah lampu andon di lorong rak — bukan poster, bukan katalog. Background parchment hangat (#F6F1E8) adalah kertas kerja gudang; ink/navy (#0F172A / #0F2540) adalah struktur; amber (#F7D60A) adalah marker presisi — garis scan, dot, hairline top-bar — yang muncul hanya untuk menuntun mata ke aksi berikutnya. Kepadatan dijaga lapang: container 1120–1180px, gap 14–16px, card bernafas. Brand hidup di detail presisi: repeating-linear top-bar (14px amber / 14px transparent), scan-line gradient yang menyapu sekali, dan dot hijau online.

Estetika adalah operasional-presisi: tipografi Instrument Sans yang tegas (800 untuk judul, 500–700 untuk body), sudut membulat lembut (12–20px) yang ramah di thumb HP, dan bayangan halus ber-offset + blur — bukan halo tanpa offset. Kecepatan scan di atas dekorasi; setiap interaksi kritis (scan, cari, mutasi, opname) harus lolos uji satu-tangan di HP.

**Key Characteristics:**
- Parchment + Ink + Marker-Amber — hangat tapi tegas, high-contrast untuk lorong gudang
- Marker Presisi — amber sebagai garis tipis/dot/scan-line, jarang tapi wajib terlihat
- Soft & Bulat — radius 12–20px, border halus, card-stack ramah thumb
- Scan-First — jalur barcode adalah jalur utama; UI mengundang scan sebelum ketik
- Operational, bukan dekoratif — bayangan punya offset+blur, motion exponential ease-out

## Colors

Parchment hangat untuk alas, ink/navy untuk struktur, amber presisi sebagai satu-satunya aksen; status memakai pasangan tint (green/red surface) yang terpisah dari amber.

### Primary
- **Andon Amber** (#F7D60A): Satu-satunya aksen. Dipakai sebagai marker presisi — repeating top-bar 3px, scan-line gradient, dot, garis hover pada quick-card, dan CTA `.btn--amber`. Tidak pernah sebagai background luas. Glow-nya `rgba(247,214,10,.14)`.
- **Amber Dim** (#E6C200): Border/ink pendamping amber untuk kontras pada tombol amber.

### Secondary
- **Ink** (#0F172A): Warna struktur utama — teks, header avatar, button primary, tile navy, dan focus ring. Juga `amber-ink` untuk teks di atas amber.
- **Navy** (#0F2540) / **Navy Deep** (#162E52): Varian ink untuk gradient tile navy dan hover state button primary. Identik dengan `andon-blue`.

### Tertiary
- **Signal Red** (#DC2626) + **Red Surface** (#FEF2F2): Status stok menipis / keluar, badge merah, alert error.
- **Signal Green** (#16A34A) + **Green Surface** (#ECFDF5): Status masuk / sukses, dot online, badge hijau.

### Secondary — SaaS (tambahan, bukan marker Andon)
- **SaaS Indigo** (#4F46E5) + **Indigo Surface** (#EEF2FF) / **Border** (#C7D2FE): Badge/status sekunder, kategori SaaS, ilustrasi — `.badge-indigo`.
- **SaaS Teal** (#0D9488) + **Teal Surface** (#CCFBF1) / **Border** (#99F6E4): Aksen sekunder alternatif (info/teal) — `.badge-teal`.
- **Aturan:** Amber tetap satu-satunya marker presisi (garis ≤3px / dot / scan-line). Indigo/teal hanya untuk status/kategori sekunder & ilustrasi SaaS, tidak untuk top-bar / scan-line / marker.

### Neutral
- **Parchment Bg** (#F6F1E8): Background halaman — kertas gudang hangat.
- **Surface** (#FFFFFF): Card, panel, header — bidang kerja bersih.
- **Surface Soft** (#F8FAFC): Row hover, icon quick-card, badge cyan/gray.
- **Surface Muted** (#EEF2F7): Varian surface untuk layering halus.
- **Line** (#E2E8F0) / **Line Strong** (#CBD5E1): Border dan divider; line-strong untuk scrollbar & pagination.
- **Muted** (#475569): Teks sekunder, label.
- **Faint** (#94A3B8): Placeholder, timestamp, meta, bottom-nav idle.

### Named Rules
**The Marker-Only Amber Rule.** Amber hanya sebagai marker presisi (garis ≤3px, dot 6–8px, scan-line) dan tombol aksi primer `.btn--amber`. Jangan pakai amber sebagai fill luas, background section, atau border tebal — raritasnya adalah fungsinya.
**The Parchment-Is-The-Page Rule.** Halaman selalu parchment (#F6F1E8), bukan putih. Putih hanya untuk permukaan yang diangkat (card/panel/header).

## Typography

**Display Font:** Instrument Sans (500, 600, 700, 800) — via Google Fonts, fallback `ui-sans-serif, system-ui`
**Body Font:** Instrument Sans (same stack)
**Label/Mono Font:** JetBrains Mono (600) untuk numerik tabular / kode

**Character:** Tegas-operasional tapi ramah. Judul extra-bold dengan tracking negatif (-0.03 s/d -0.05em) untuk kehadiran; body 13–14px longgar (1.5) untuk scan cepat; label 10–11px uppercase dengan tracking lebar (.10–.14em) sebagai penanda seksi, bukan hiasan.

### Hierarchy
- **Display** (800, clamp 1.375rem–1.75rem / 22–28px, 0.95, -0.04em): Hero heading `.andon-hero__copy h1` — satu per halaman, di atas kertas parchment.
- **Headline** (800, 22px, 1.1, -0.03em): Judul halaman `.andon-page-title`.
- **Title** (700, 13px, 1.3, -0.02em): Nama barang / label tile — `strong` di row & card.
- **Body** (500–600, 14px desktop / 15px mobile input, 1.5, -0.01em): Paragraf, deskripsi, isi tabel. Measure ideal 48–65ch di copy hero.
- **Label** (800, 10–11px, 1.2, 0.10–0.14em, uppercase): Kicker, tile label, panel head h2, form label `.andon-label`.
- **Mono/Numeric** (600 JetBrains Mono, tabular-nums): Nilai tile `.andon-tile__value` (26–34px, -0.05em), kode/SKU jika ada.

### Named Rules
**The One Display Rule.** Hanya satu Display (hero h1) per viewport. Semua judul lain turun ke Headline/Title — tidak ada kompetisi ukuran.
**The Label-Is-Signal Rule.** Label uppercase hanya untuk penanda struktur (kicker, panel head, tile label), bukan untuk body copy.

## Layout

Grid longgar-modular: container 1120px (dashboard) hingga 1180px (site-main), padding 20–24px desktop / 14px mobile. Rhythm berbasis 6–8px (gap 10, 12, 14, 16, 20px). Section grouping ketat di dalam card (padding 14–22px), separation lega antar section (20px).

Hero adalah 1.35fr + 0.9fr (copy + scan card) di desktop, collapse ke 1 kolom di ≤980px. Tiles 1.6fr + 1fr + 1fr + 1fr (hero metric + 3 support), jadi 2 kolom di tablet dan 2 kolom dengan first-child full-width di ≤560px. Quick actions 6 kolom (admin) / 5 kolom (staff), turun ke 3 lalu 2 kolom di mobile kecil. Content grid 1.15fr + 0.85fr (ringkasan + mutasi), collapse ke 1 kolom.

Mobile adalah warga utama: bottom-nav fixed (52px + safe-area), inputs 48px min-height, buttons 44px, tabel flip ke card-stack (thead hidden, td jadi flex row dengan `data-label` sebagai label kiri). Semua interaksi kritis harus reachable thumb.

## Elevation & Depth

Sistem memakai **shadow halus ber-offset + blur**, bukan flat murni dan bukan halo dekoratif. Kedalaman datang dari bayangan lembut di atas parchment, bukan dari border tebal.

### Shadow Vocabulary
- **Soft Lift** (`0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04)` — `--andon-shadow`): Default untuk panel, hero copy, card. Mengangkat permukaan putih dari parchment tanpa mengeras.
- **Strong Lift** (`0 10px 28px rgba(15,23,42,.08), 0 2px 6px rgba(15,23,42,.05)` — `--andon-shadow-strong`): Untuk scan card navy dan hover card — sedikit lebih dalam, tetap blur.
- **Quick Shadow** (`0 2px 12px rgba(15,23,42,.04)`): Untuk quick-card `.andon-q` — tipis, hampir flat, naik ke Soft Lift saat hover.

### Named Rules
**The Offset-Plus-Blur Rule.** Setiap shadow wajib punya offset Y dan blur — zero-offset halo atau hard block shadow tidak ada di sistem ini.
**The Hover-Lifts Rule.** Hover mengangkat 1–3px (`translateY(-1px)`) dan memperdalam shadow; tidak ada perubahan warna besar — gerak + bayangan, bukan repaint.

## Shapes

Bahasa bentuk adalah **soft & bulat presisi**: sudut besar tapi tidak playful, border tipis, dan underline marker tipis sebagai aksen.

- **Radius Scale:** sm 12px (button, input focus, avatar, pagination), md 16px (tile, quick-card), lg 18px (panel, hero, scan card — `--andon-radius`), xl 20px (`.andon-card`), pill 999px (badge, dot).
- **Input Radius:** 14px — sedikit lebih kecil dari card untuk hierarki.
- **Border:** 1px solid Line (#E2E8F0) default; Line-Strong atau Ink saat hover/focus. Tidak ada border-left tebal sebagai aksen — aksen hanya via hairline top (`::before` 1px) atau bottom marker 2px pada quick-card.
- **Clipping:** Tidak ada mask geometrik organik; sudut konsisten rounded, tidak ada cutout atau polygon.
- **Marker Geometry:** Top-bar repeating-linear 14px dash, scan-stripe diagonal 10px, scan-line 2px gradient.

## Components

### Buttons
Potongan presisi dengan radius lembut, hover naik 1px + shadow.

- **Shape:** Rounded sm (12px), min-height 38px (44px di mobile), padding 0 16px, gap 6px, font 13px/700, transition 140–160ms.
- **Primary** (Ink): `bg #0F172A, text #fff, border ink`. Hover → `bg #162E52, lift -1px, shadow`.
- **Ghost** (Surface): `bg #fff, text ink, border line-strong`. Hover → `border ink, lift + shadow`.
- **Amber** (Marker): `bg #F7D60A, text ink, border amber-dim, shadow amber-glow 0 4px 16px`. Hover → `brightness .98, lift`. Dipakai hemat — satu per hero.
- **Danger** (Red tint): `bg #FEF2F2, text #DC2626, border #FECACA`. Hover → `bg #FEE2E2`.
- **Focus:** `outline 2px solid navy, offset 2px, radius 4px`.

### Chips
Tidak ada chip/filter system dominan; badge dipakai sebagai status pill.

- **Style:** Pill 999px, padding 3px 9px, font 11px/700, border 1px solid tint. Blue/Purple `#EFF6FF / #1E3A5F`, Green `#ECFDF5 / #065F46`, Red `#FEF2F2 / #991B1B`, Yellow `#FEFCE8 / #854D0E`, Gray `#F8FAFC / #475569`.
- **State:** Static — tidak ada selected/unselected; varian warna mengkode status stok/mutasi.

### Cards / Containers
Bidang putih lembut yang mengapung di parchment.

- **Corner Style:** 16–20px (tile 16px, panel 18px, card 20px).
- **Background:** `#FFFFFF` (panel/header), `#F8FAFC` untuk icon well.
- **Shadow Strategy:** Soft Lift default, Strong Lift untuk scan card, Quick Shadow untuk quick-grid; hover naik ke level berikutnya.
- **Border:** 1px solid `#E2E8F0` (atau `#EDEEF2` untuk card ringkas). Hairline top `::before 1px` (line-strong default, amber untuk tile amber, red untuk alert) — bukan border-left tebal.
- **Internal Padding:** Tile 16px 14px 12px, Panel head 12px 16px, Card 16px, Data row 10px 0.
- **Quick Card (`.andon-q`):** 16px radius, icon 44px (40px mobile) rounded 12px, bottom marker 2px amber saat hover, icon invert ke ink+amber saat hover.

### Inputs / Fields
Field lembut dengan background tint dan focus ink.

- **Style:** `bg #FBFBFD, border 1px #E8EAF0, radius 14px, padding 11px 14px, font 14px, color ink` — select punya custom chevron SVG di kanan.
- **Focus:** `bg #fff, border ink, shadow 0 0 0 3px rgba(15,23,42,.06)` — tanpa glow amber.
- **Placeholder:** `var(--andon-faint) #94A3B8`.
- **Label:** 11px/700, tracking .06em, uppercase, muted, margin-bottom 6px.
- **Error / Disabled:** Alert error `#FEF2F2 / #991B1B`; disabled belum bertoken — fallback `opacity .55, pointer-events none`.
- **Mobile:** min-height 48px, font 15px untuk cegah zoom iOS.

### Navigation
- **Site Header:** Sticky top, `bg #FFFFFF, border-bottom 1px line`, bar 3px repeating amber dash, inner 1180px. Avatar 34px rounded 8px ink+amber, name 13px/700, meta 10px/700 tracking .10em faint uppercase.
- **Bottom Nav (mobile ≤640px):** Fixed bottom, `bg #FFFFFF, border-top 1px line, shadow 0 -4px 20px`, safe-area aware. Item 52px, icon 22px, label 10px/700 tracking .04em faint. Active → `color ink + 2px amber underline (::after)`. Scan item `--scan` punya surface bg + border; active scan invert ke `bg amber, text ink`. Hover/active hanya color+bg — tidak ada scale besar.
- **Pagination:** Pill 10px radius, 34px min, border line, bg surface. Hover → surface-soft, active `bg ink, text #fff, border ink`.

### Scan Card (Signature Component)
Kartu navy magnetik untuk aksi scan.

- **Style:** `bg ink (#0F172A), text #fff, radius 18px, padding 18px, shadow strong`, overflow hidden. `::after` repeating amber stripe 10px diagonal di corner, `::before` scan-line 2px gradient amber yang sweep sekali (2.2s, delay 1.2s, forwards) — respects `prefers-reduced-motion`.
- **Label:** 10px/800 tracking .14em amber + 8px dot + glow.
- **CTA:** 12px/800 tracking .06em amber, gap melebar 8→12px saat hover. Hover card lift -2px.

## Do's and Don'ts

### Do:
- **Do** pakai amber hanya sebagai marker presisi (≤3px line / 6–8px dot / scan-line) — raritasnya adalah sinyal.
- **Do** jaga halaman di parchment #F6F1E8; putih hanya untuk permukaan terangkat.
- **Do** pertahankan radius lembut 12–20px dan shadow dengan offset+blur — soft & bulat, bukan kotak keras.
- **Do** buat input 48px di mobile dan button 44px — thumb-friendly di lorong rak.
- **Do** pakai Instrument Sans 800 dengan tracking negatif untuk judul; body 1.5 untuk scan cepat.
- **Do** invert quick-icon ke ink+amber saat hover dan pakai bottom 2px amber marker — feedback presisi.
- **Do** hormati `prefers-reduced-motion` — matikan entrance & scan-line animation.

### Don't:
- **Don't** pakai amber sebagai background luas, fill section, atau border tebal — itu menghancurkan hierarki marker.
- **Don't** pakai `border-left` atau `border-right` tebal (>1px) sebagai aksen pada card/list — aksen hanya hairline top atau bottom marker tipis.
- **Don't** buat shadow tanpa offset atau tanpa blur (halo dekoratif / hard 4px block) — bukan bagian dari sistem ini.
- **Don't** tambah kicker/eyebrow di atas heading di luar `.andon-kicker` hero yang sudah ada — heading harus berdiri sendiri.
- **Don't** pakai gradient text, glass/blur dekoratif, atau shadow berwarna — kedalaman hanya dari tinta + blur halus.
- **Don't** pakai monospace untuk gaya "teknis" di luar kode/numerik tabular — Instrument Sans adalah voice sistem.
- **Don't** invent warna di luar palet (amber/navy/green/red + indigo/teal sekunder) — sekunder baru harus lewat DESIGN.md; amber tetap satu-satunya marker presisi.
