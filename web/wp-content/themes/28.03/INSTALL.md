# ou — WordPress Portfolio Theme
### Setup & Customization Guide

---

## Quick Install

1. Upload `ou-theme.zip` via **WP Admin → Appearance → Themes → Add New → Upload Theme**
2. **Activate** the theme
3. **Settings → Reading** → "Your homepage displays" → Static page → select any page as Homepage
4. **Appearance → Customize** to fill in all content
5. Add projects via **Portfolio → Add New**

---

## File Structure

```
ou-theme/
├── style.css               ← WP theme header (metadata only)
├── functions.php           ← Setup, CPT, enqueues, customizer, data helper
├── header.php              ← Loader, topbar (logo + toggles), wild slider shell
├── footer.php              ← Bottom navigation (6 links), mobile drawer
├── front-page.php          ← One-page homepage (hero, list, about, contact)
├── single.php              ← Single project + blog post template
├── index.php               ← Archive/blog fallback
├── 404.php                 ← Not found page
│
└── assets/
    ├── css/
    │   ├── variables.css   ← ALL tokens: colors, fonts, spacing, motion
    │   ├── base.css        ← Reset, type utilities, reveal classes
    │   ├── loader.css      ← Preloader + grain canvas + cursor
    │   ├── nav.css         ← Topbar + bottom nav + mobile drawer
    │   ├── hero.css        ← Hero section, video bg, status row
    │   ├── clean.css       ← CLEAN mode: list, about, contact, footer
    │   ├── wild.css        ← WILD mode: fullscreen slider
    │   └── responsive.css  ← Breakpoints, reduced motion
    │
    └── js/
        ├── grain.js        ← 35mm film grain at 24fps
        ├── cursor.js       ← Dot + lerp ring cursor
        ├── theme.js        ← Dark ↔ Light toggle + localStorage
        ├── mode.js         ← CLEAN ↔ WILD toggle + localStorage
        ├── filter.js       ← Category filter (both modes)
        ├── slider.js       ← WILD: drag/touch/keyboard cinematic slider
        ├── scroll.js       ← Loader, reveals, nav tracking, preview image
        └── main.js         ← Entry point
```

---

## The Two Modes

### CLEAN Mode (default)
Editorial numbered project list — inspired by Corentin Bernadou and cargo.site.
- Numbered rows with title, category, year
- Floating image preview on hover (follows cursor)
- Scroll-triggered row reveals
- Infinite categories via nav filter links

### WILD Mode
Fullscreen cinematic slider — inspired by voku.studio and Cathy Dolle.
- Full-viewport project slides with parallax media
- Word-by-word title clip animation per slide
- Giant ghost category text in background
- Drag, touch-swipe, keyboard (←→), dot + arrow navigation
- Auto-advances every 6 seconds, pauses on interaction

**Toggle:** top-right button labeled "Wild ↗" / "Clean ↙"
Both mode + theme preferences persist in `localStorage`.

---

## Customization: 4 Key Replacements

### 1. LOGO
**File:** `header.php` — search `REPLACE: logo`

The placeholder is two crossed lines (an X mark). Replace with:

**Option A** — Upload via Customizer:
`Appearance → Customize → Site Identity → Logo`

**Option B** — Swap SVG in `header.php`:
```html
<!-- Replace the <svg> block with your own SVG or <img> -->
<svg class="topbar__logo-mark" viewBox="0 0 28 28" ...>
  <!-- your paths here -->
</svg>
```

The hero uses the same logo slot — add your animated SVG in `front-page.php` inside `.hero__content` if desired.

---

### 2. HERO VIDEO
**Via Customizer:** `Appearance → Customize → Hero Section → Hero Video URL`
**File:** `front-page.php` — search `REPLACE: video`

Steps:
1. Upload `.mp4` to **Media → Add New**
2. Copy the file URL
3. Paste into Customizer → Hero Section → Hero Video URL

Recommended: `1920×1080`, H.264, under 15MB, no audio.
Compress with HandBrake: CRF 26–28, web-optimized.

---

### 3. FONTS
**File:** `assets/css/variables.css` — search `REPLACE: fonts`
**File:** `functions.php` — search `REPLACE: font import`

**Step 1 — Change Google Fonts import in `functions.php`:**
```php
// Current (Inter):
wp_enqueue_style( 'ou-fonts',
    'https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400&display=swap', ...);

// Example — switch to Neue Montreal or similar:
wp_enqueue_style( 'ou-fonts',
    'https://fonts.googleapis.com/css2?family=DM+Sans:wght@200;300;400&display=swap', ...);
```

**Step 2 — Update `variables.css`:**
```css
--font-display: "DM Sans", sans-serif;
--font-body:    "DM Sans", sans-serif;
```

**For self-hosted / custom fonts (e.g. Alte Haas Grotesk):**
```css
/* Add to variables.css */
@font-face {
    font-family: "Alte Haas Grotesk";
    src: url("../fonts/AlteHaasGroteskRegular.woff2") format("woff2");
    font-weight: 400;
    font-display: swap;
}
--font-display: "Alte Haas Grotesk", sans-serif;
--font-body:    "Alte Haas Grotesk", sans-serif;
```
Place `.woff2` files in `ou-theme/assets/fonts/`.

---

### 4. ACCENT COLORS
**File:** `assets/css/variables.css`

```css
[data-theme="dark"] {
  --accent: #FFD600;   /* ← Yellow on black. Change this. */
}
[data-theme="light"] {
  --accent: #0057FF;   /* ← Blue on white. Change this. */
}
```

For monochrome (no accent):
```css
--accent: var(--fg);
```

---

## Navigation Setup

The bottom nav has 6 links: **About · Web · Video · Photo · Graphic · Contact**

Each link has two attributes:
- `href="#ax-about"` / `href="#ax-clean"` / `href="#ax-contact"` → smooth scrolls in CLEAN mode
- `data-filter="web"` / `"video"` / `"photo"` / `"graphic"` / `"all"` → filters projects in both modes

**To rename or reorder links**, edit the `<ul class="nav__links">` in `footer.php`.

---

## Adding Portfolio Projects

1. **WP Admin → Portfolio → Add New**
2. Fill in:
   | Field | Where |
   |-------|-------|
   | Title | Post title |
   | Description | Post body (editor) |
   | Featured Image | Used as slider bg + hover preview + single hero |
   | **Project Type** (taxonomy) | Sidebar → determines which nav filter shows it |
   | Year | Custom Fields → `ou_year` |
   | Client | Custom Fields → `ou_client` |
   | Role | Custom Fields → `ou_role` |
   | Tools | Custom Fields → `ou_tools` |
   | Live URL | Custom Fields → `ou_url` |
3. Set **Order** (Page Attributes → Order) to control display sequence
4. Publish

> **Enable Custom Fields panel:** three-dot menu (⋮) top right → Preferences → Panels → Custom Fields ✓

### Project Type Taxonomy Slugs
Use these exact slugs when creating terms (they match the nav filter values):

| Term name | Slug | Nav link |
|-----------|------|----------|
| Web Design | `web` | Web |
| Video | `video` | Video |
| Photography | `photo` | Photo |
| Graphic Design | `graphic` | Graphic |

Add any custom types — they'll appear as unfiltered items (visible under "All").

---

## Scroll Reveal System

Use `data-reveal` + optional `data-delay` on any element:

```html
<div data-reveal="up">Fades up</div>
<div data-reveal="left">Slides from right</div>
<div data-reveal="right">Slides from left</div>
<div data-reveal="scale">Scales in</div>
<div data-reveal="fade">Fades only</div>

<!-- Stagger (0.08s per step) -->
<div data-reveal="up" data-delay="1">First</div>
<div data-reveal="up" data-delay="2">Second</div>
<div data-reveal="up" data-delay="3">Third</div>
```

---

## WILD Slider: Auto-Advance

Default: 6 seconds per slide. To change, open `assets/js/slider.js`:
```js
var AUTO_DELAY = 6000; // milliseconds — change this value
```

---

## Film Grain

Intensity controlled in `assets/css/variables.css`:
```css
[data-theme="dark"]  { --grain: 0.06; }  /* 0 = off, 0.1 = strong */
[data-theme="light"] { --grain: 0.025; }
```

To disable entirely, remove `AxGrain.init()` from `assets/js/main.js`.

---

## Contact Form

The theme outputs a contact section with links only. To add a form:

1. Install **Contact Form 7** (free)
2. Create a form, note its ID
3. In `front-page.php`, find `#ax-contact` and add before the footer strip:
   ```php
   <?php echo do_shortcode('[contact-form-7 id="YOUR_ID" title="Contact"]'); ?>
   ```

---

## Recommended Image Sizes

| Use | Dimensions | Ratio |
|-----|-----------|-------|
| WILD slider background | 1920 × 1080 | 16:9 |
| Single project hero | 1920 × 1080 | 16:9 |
| Project thumbnail (detail) | 800 × 600 | 4:3 |
| Hover preview image | 480 × 320 | 3:2 |

Use `.webp` format — WordPress auto-converts on upload.

---

## Performance

- Compress hero video: HandBrake → H.264, CRF 27, Web Optimized ✓
- Install **ShortPixel** or **Smush** for image compression
- Enable server-side caching (LiteSpeed Cache, W3 Total Cache)
- The grain canvas runs at half display resolution for performance

---

## Browser Support
Chrome 90+ · Firefox 88+ · Safari 14+ · Edge 90+

Custom cursor is automatically hidden on touch devices (`hover: none`).
All animations respect `prefers-reduced-motion`.
