---
name: Academic Biometric Ledger
colors:
  surface: '#fff9ed'
  surface-dim: '#dfd9ce'
  surface-bright: '#fff9ed'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f9f3e7'
  surface-container: '#f3ede1'
  surface-container-high: '#ede8dc'
  surface-container-highest: '#e8e2d6'
  on-surface: '#1d1c14'
  on-surface-variant: '#45474b'
  inverse-surface: '#333028'
  inverse-on-surface: '#f6f0e4'
  outline: '#75777c'
  outline-variant: '#c5c6cb'
  surface-tint: '#595f69'
  primary: '#0d131c'
  on-primary: '#ffffff'
  primary-container: '#222831'
  on-primary-container: '#898f9a'
  inverse-primary: '#c1c7d3'
  secondary: '#5a5f67'
  on-secondary: '#ffffff'
  secondary-container: '#dee2ed'
  on-secondary-container: '#60656d'
  tertiary: '#191308'
  on-tertiary: '#ffffff'
  tertiary-container: '#2e271b'
  on-tertiary-container: '#998e7d'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dde3ef'
  primary-fixed-dim: '#c1c7d3'
  on-primary-fixed: '#161c25'
  on-primary-fixed-variant: '#414751'
  secondary-fixed: '#dee2ed'
  secondary-fixed-dim: '#c2c7d0'
  on-secondary-fixed: '#171c23'
  on-secondary-fixed-variant: '#42474f'
  tertiary-fixed: '#eee1ce'
  tertiary-fixed-dim: '#d2c5b3'
  on-tertiary-fixed: '#211b0f'
  on-tertiary-fixed-variant: '#4e4638'
  background: '#fff9ed'
  on-background: '#1d1c14'
  surface-variant: '#e8e2d6'
typography:
  display-lg:
    fontFamily: Bricolage Grotesque
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.03em
  display-lg-mobile:
    fontFamily: Bricolage Grotesque
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Bricolage Grotesque
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Bricolage Grotesque
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Bricolage Grotesque
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Bricolage Grotesque
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 26px
    letterSpacing: '0'
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
  label-lg:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-technical:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 14px
    letterSpacing: 0.06em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-mobile: 0.75rem
  margin: 2rem
  margin-mobile: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
---

## Brand & Style

This design system establishes an architectural bridge between institutional academic heritage and forensic biometric precision. Designed specifically for university lecture halls, administrative audit desks, and department checkpoints, the interface discards generic utilitarian SaaS tropes in favor of an authoritative, parchment-grounded instrument.

The design philosophy combines:
- **Warm Editorial Academic Elegance:** Cream, paper, and sand surfaces reminiscent of archival ledgers, fine stationery, and architectural stone foundations.
- **Biometric Optical Precision:** Calibrated 1px hairline rules, technical reticle corner brackets, monospaced metadata stamps, and high-legibility tabular telemetry.
- **Restraint and Gravitas:** Absence of arbitrary electric accents, blues, purples, or glossy neon. Dark ink tones are reserved strictly for high-contrast focal points: typography, structural navigation anchors, terminal camera enclosures, and authoritative actions.

The resulting experience inspires institutional trust, audit clarity, and calm efficiency during fast-paced student verification passes.

## Colors

The palette operates under strict natural light hierarchy, grounding the entire interface on tactile paper foundations rather than harsh digital white.

### Foundational Palette
- **Paper Background (`#F7F1E5`):** The universal root canvas tone. Warm, low eye-strain, non-reflective.
- **Sand Light (`#EFE6D5`):** Raised card surfaces, panel fills, container layers, and input field backgrounds.
- **Sand (`#DFD0B8`):** Structural 1px hairline borders, field strokes, and active segment backgrounds.
- **Stone Soft (`#C9BFAE`):** Inactive track borders, pressed state fills, and table cell dividing lines.
- **Stone (`#948979`):** Secondary meta labels, timestamp captions, icon glyphs, reticle marks, and pending state tags.
- **Slate (`#393E46`):** Secondary headings, subtext, high-contrast secondary controls, and toolbar containers.
- **Primary Ink (`#222831`):** Primary headings, high-priority button surfaces, technical optical frames, and deep navigational containers.

### Semantic Status Tokens
Biometric audit verification requires distinct, muted-natural states with high legibility against sand surfaces:
- **Present (`#4F7A5A`):** Deep botanical moss. Signifies verified identity, low-latency match, and enrolled status.
- **Late (`#B7791F`):** Architectural ochre/amber. Signifies verified identity past the threshold grace window.
- **Absent (`#A8403A`):** Brick red/madder ink. Signifies missed roll-call, non-detection, or administrative override.
- **Pending / Calibrating (`#948979`):** Neutral archival stone. Signifies queued ingestion, active camera scan, or unverified ledger rows.

### Usage Guardrails
- Dark backgrounds are prohibited on page-level roots; they are strictly confined to the optical camera viewfinder portal, global drawer navigation docks, and primary action triggers.
- Status colors must never be presented as neon fills; they appear as tinted badges on Sand Light backgrounds with crisp 1px borders, or as 8px verification status pills.

## Typography

The typographic hierarchy intentionally pairs editorial academic distinction with clean UI legibility.

- **Headings & Display:** Rendered in **Bricolage Grotesque**. Its idiosyncratic humanist stroke terminations convey historical academic scholarship, while its geometric chassis gives biometric dashboards a sharp, bespoke identity.
- **Body, Ledger Data, and Controls:** Rendered in clean, neutral grotesque UI typography with standard open counters, high x-height, and precise glyph shapes.
- **Data Tables & Biometric Timestamps:** All attendance logs, confidence coefficients (e.g., `99.4%`), student registration numbers (`UID-2024-889`), and time logs must strictly employ CSS `font-variant-numeric: tabular-nums lining-nums`. This prevents layout shifts during live frame-by-frame face inference.
- **Technical Meta Stamps:** Captions, scan status indicators, and camera metrics use `label-technical` in uppercase with subtle letter-spacing (`0.06em`) to echo mechanical instrument calibration plates.

## Layout & Spacing

The layout is grounded in a disciplined 8pt architectural rhythm, structured to handle dual workloads: high-density ledger matrices and real-time live video streams.

### Grid Framework
- **Desktop (12 Columns, Max 1440px):** 24px (`1.5rem`) gutters with 32px (`2rem`) outer page margin. Split-pane layout splits the live scanning viewport (5 columns) from the live stream verification roll (7 columns).
- **Tablet (8 Columns, 768px - 1024px):** 16px (`1rem`) gutters with 24px (`1.5rem`) margin. Stacked modules featuring a persistent top viewfinder anchor and collapsible session logs.
- **Mobile (4 Columns, <768px):** 12px (`0.75rem`) gutters with 16px (`1rem`) margins. Camera feed transitions into a sticky upper card with card-based attendance drawers below.

### Rhythmic Discipline
- Components leverage structural hairline partitions (`border: 1px solid #DFD0B8`) over arbitrary empty margins.
- Internal component density is kept tight: `space-sm` (8px) between metadata rows, `space-md` (16px) container padding, and `space-xl` (32px) separating primary academic cohorts.

## Elevation & Depth

Visual hierarchy avoids generic blurred drop shadows. Depth is achieved via **tonal stratification, subtle warm pigment occlusion, and architectural hairline definition**.

### Layering Levels
- **Canvas (Ground 0):** Paper Background (`#F7F1E5`). Raw textured surface.
- **Level 1 (Panels, Rows, Shells):** Sand Light (`#EFE6D5`) framed by a continuous `1px solid #DFD0B8` border.
- **Level 2 (Active Cards, Popovers, Flyout Modals):** Sand Light (`#EFE6D5`) elevated with a dual-tinted pigment shadow:
  `box-shadow: 0 1px 3px rgba(34, 40, 49, 0.04), 0 8px 24px rgba(148, 137, 121, 0.12);`
  The shadow is warmly tinted by the Stone and Sand pigments, avoiding cold grey halos.
- **Level 3 (Primary Focus & Hardware Viewport):** Deep Ink (`#222831`) background with an inset 1px border (`rgba(223, 208, 184, 0.2)`), creating a sharp optical lens effect against the warm parchment canvas.

### Biometric Focus & Framing
Rather than elevation through dramatic z-index shadows, spatial focus is created through **precision corner brackets (reticles)**: 8px L-shaped markers placed at the four corners of biometric bounding boxes, active student cards, and video viewport perimeters, styled in `1.5px solid #222831` or the respective status hue.

## Shapes

The design system adopts a disciplined **Soft (`1`) shape profile**. Radii are intentionally restrained to preserve the serious, academic identity and align with structural hairline borders.

- **Micro Controls & Badges (`rounded-sm`):** `2px` to `4px`. Crisp, architectural, avoiding bubbly SaaS treatments.
- **Standard Surfaces & Inputs (`rounded`):** `4px` (`0.25rem`). Standard for text fields, interactive buttons, modal frames, and biometric roster rows.
- **Enclosures & Camera Portals (`rounded-lg`):** `8px` (`0.5rem`). Applied to the central camera viewfinder, large session analytics panels, and floating identification cards.
- **Status Pills (`rounded-full`):** Reserved exclusively for live operational indicators (e.g., "LIVE INFERENCE", "SYNCING") and circular faculty avatar anchors.

## Components

### Buttons & Interactive Controls
- **Primary Button:** Primary Ink (`#222831`) fill, Paper (`#F7F1E5`) text, 4px border radius. Micro-interaction: Hover shifts background to Slate (`#393E46`) with an active scale of `0.99`.
- **Secondary / Ledger Action:** Sand Light (`#EFE6D5`) fill, Slate (`#393E46`) text, 1px solid Sand (`#DFD0B8`). Hover introduces Stone Soft (`#C9BFAE`) border.
- **Ghost Action:** Transparent background, Stone (`#948979`) text, shifting to Primary Ink on hover with an underline hairline.

### Biometric Viewfinder Container
- Dedicated hardware viewport wrapped in Primary Ink (`#222831`) with a subtle 1px border (`#393E46`).
- **Face Reticle Target:** 4 corner brackets (`#DFD0B8` or Semantic Status color) bounding the detected facial coordinate.
- **Telemetry HUD:** Monospaced tabular status label positioned directly below the reticle box displaying: Student ID, Timestamp (`HH:MM:SS.ms`), and Confidence Metric (`98.7% MATCH`).

### Attendance Ledger Table & Rows
- Alternating or clean-lined row layouts with `1px solid #DFD0B8` horizontal separators.
- Columns: Checkpoint Thumbnail (32px, `rounded-sm`), Full Name (Bricolage Grotesque, 500 weight), Department/ID (`Inter`, tabular numbers), Timestamp (`Inter`, tabular numbers), Confidence Factor, and Status Pill.
- Row hover: Sand Light (`#EFE6D5`) highlight transition across 120ms ease-out.

### Status Indicators & Chips
- **Chips:** Built on Sand Light (`#EFE6D5`) backgrounds with a 1px border matching the status color at 40% opacity. 
- Status indicator dots are 6px solid circles accompanied by explicit uppercase technical labels (`PRESENT`, `LATE`, `ABSENT`, `PENDING`).

### Input Fields & Search Queries
- Surface fill: Sand Light (`#EFE6D5`).
- Border: 1px solid Sand (`#DFD0B8`).
- Focus state: Replaces border with Slate (`#393E46`) at 1.5px; zero neon glow; accompanied by a crisp 2px offset Sand outline.
- Typography: Tabular numerals default for student registration lookups.

### Verification Cards
- Self-contained modal or side-drawer cards displaying instantaneous face detection results.
- Top section contains a side-by-side comparison: Enrolled Archival Photo vs. Live Capture Frame.
- Separation is handled by a dotted 1px rule (`#C9BFAE`).
- Includes direct manual override triggers (Mark Present, Flag for Review) styled in clean, low-height secondary buttons.