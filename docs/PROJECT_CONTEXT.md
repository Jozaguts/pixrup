# Pixrup Project Context (Updated Scope)

This file captures the current scope and module definitions for this version of Pixrup.
Use it as the source of truth when comparing older PDFs or legacy docs.

## In-scope modules (current build)

- Overview: Property snapshot and signals using HouseCanary data and internal aggregation.
- PixrWorth: Property value estimate and comparables (HouseCanary).
- PixrGlowUp: Before/after image generation using Replicate.
- PixrSpyHunt: Market comps and signals using RentCast.

## Out-of-scope modules (this version)

- Matterport / Virtual Verify
- PixrSeal (Reports)
- PixrCollab (Live collaboration)

These are not part of the current delivery scope and should not be treated as active requirements.

## Module naming clarifications

- PixrVision: In this version it refers to the PDF report generation that compiles and analyzes
  data from all modules. It is not the Matterport 3D tour module.
- PixrSeal: Report builder UI is present but full export pipeline is out of scope here.

## Provider decisions (current)

- SpyHunt uses RentCast (not Zillow).
- Glow-Up uses Replicate (not Renovate AI).

## Notes on legacy PDFs

Older PDFs may mention Zillow, Renovate AI, Matterport, Firebase, or DocuMint workflows.
Those references are legacy and should be treated as future-scope unless explicitly re-approved.

# Pixrup — Pricing Structure & Stripe Subscription Flow

## Overview
Pixrup uses **Stripe Checkout + Subscriptions** with **strict server-side enforcement** in Laravel.  
Each plan unlocks features via middleware, quotas, and feature flags synced from Stripe webhooks.

---

## Plans

### 1. Micro — $5 (One-time)
**Use case:** Quick, anonymous, single task.

**Includes**
- Upload **1 file** (image / screenshot / PDF)
- AI reads & narrates content (TTS)
- Download MP3 / PDF
- No account required

**Stripe**
- Product: `PIX_MICRO`
- Price: one-time
- Checkout only (no subscription)

**Enforcement**
- No user created
- No dashboard access
- Download link expires in **24h**
- No API keys

---

### 2. Starter — $99 / month
**Use case:** Individuals starting with Pixrup.

**Includes**
- Dashboard access
- Project & history tracking
- **50 documents / month**
- Basic support
- Pixrup branding always visible

**Stripe**
- Product: `PIX_STARTER`
- Price: recurring (monthly)
- Subscription via Stripe Checkout

**Enforcement**
- `plan_tier = PRICE_STARTER`
- `used_docs` counter (monthly reset)
- Branding **cannot** be removed
- Pro / Enterprise routes blocked

---

### 3. Pro — $199 / month
**Use case:** Power users & professionals.

**Includes**
- Everything in Starter
- **Unlimited documents**
- Branding tab (logo + colors)
- AI Before/After renders (GlowUp)
- Priority support
- “Powered by Pixrup” footer enforced

**Stripe**
- Product: `PIX_PRO`
- Price: recurring (monthly)

**Enforcement**
- `plan_tier = PRICE_PRO`
- Branding enabled (partial)
- **20 AI renders / month**
- White-label still blocked

---

### 4. Enterprise — $499 / month
**Use case:** Teams, agencies, integrations.

**Includes**
- Full white-label PDFs
- Custom logo, colors, contact block
- Multi-user admin (default: 10 users)
- API key access
- Audit logs & data retention
- Dedicated account manager

**Stripe**
- Product: `PIX_ENTERPRISE`
- Price: recurring (monthly)

**Enforcement**
- `plan_tier = PRICE_ENTERPRISE`
- `white_label = true`
- `/api/*` routes unlocked
- Unlimited renders & docs

---

## Feature Matrix

| Feature | Starter | Pro | Enterprise |
|------|------|------|------|
| Dashboard | ✅ | ✅ | ✅ |
| Docs / month | 50 | Unlimited | Unlimited |
| AI Renders | ❌ | 20 / mo | Unlimited |
| Branding | ❌ | Partial | Full |
| White-label | ❌ | ❌ | ✅ |
| API Access | ❌ | ❌ | ✅ |
| Multi-user | ❌ | ❌ | ✅ |

---

## Stripe Subscription Flow

1. User selects a plan on `/pricing`
2. Redirect to **Stripe Checkout**
3. Payment completed
4. Stripe sends webhook → `/stripe/webhook`
5. Laravel Cashier updates:
  - `stripe_customer_id`
  - `stripe_subscription_id`
  - `plan_tier`
6. Access & limits update immediately

**If payment fails**
- Stripe webhook downgrades plan
- Middleware blocks restricted features instantly

---

## Laravel Enforcement Layers

### 1. Plan Middleware
```php
Route::middleware(['auth','plan:PRICE_PRO,PRICE_ENTERPRISE'])

# PixrWorth — Module Definition, Pricing Impact & Stripe Enforcement

## Purpose
**PixrWorth** is the valuation engine of Pixrup.  
It provides **property value intelligence** using external data providers (e.g. AVM, comps, census, risk signals) and converts raw data into **clear valuation outputs** for users and downstream modules.

PixrWorth is **read-only + analytical** (no image generation).

---

## What PixrWorth Delivers

### Core Outputs
- Estimated Property Value (AVM-style)
- Value range (low / mid / high confidence)
- Market context (area, county, MSA)
- Risk & confidence indicators
- Historical signals (when available)

### Typical Use Cases
- Property snapshot before analysis
- Baseline for PixrVision findings
- Input for reports, PDFs, exports
- Pre-renovation value (used by GlowUp later)

---

## Data Sources (Conceptual)
- Property attributes (address, geo)
- Market data (comps, census, crime)
- Risk zones (flood, disaster, FEMA)
- Historical valuation signals

> PixrWorth **does not** modify data — it interprets and summarizes.

---

## Pricing & Plan Access

### Micro — ❌ Not Included
PixrWorth is **not available** in Micro.
- Reason: requires API calls + structured analysis
- Micro is single-file, anonymous, one-off

---

### Starter — ✅ Limited Access
**Included with limits**

**Capabilities**
- Basic property valuation
- Single-value estimate
- Limited market context
- PDF export with Pixrup branding

**Limits**
- Counts as **1 document**
- No advanced breakdowns
- No historical comparison

**Enforcement**
- `plan_tier = PRICE_STARTER`
- Uses `used_docs` counter
- Advanced routes blocked

---

### Pro — ✅ Full Valuation
**Primary PixrWorth plan**

**Capabilities**
- Full valuation range (low / mid / high)
- Market & regional context
- Confidence indicators
- Historical signals (when available)
- Exportable PDF (partial branding)

**Limits**
- Unlimited valuations
- Included in doc generation
- Can be reused across reports

**Enforcement**
- `plan_tier = PRICE_PRO`
- No valuation cap
- Branding footer enforced

---

### Enterprise — ✅ Advanced + API
**White-label & automation**

**Capabilities**
- Everything in Pro
- White-label valuation reports
- API access to PixrWorth results
- Bulk property evaluation
- Audit logs

**Limits**
- None (fair-use)
- API rate limits configurable

**Enforcement**
- `plan_tier = PRICE_ENTERPRISE`
- `/api/pixrworth/*` unlocked
- `white_label = true`

---

## Feature Matrix

| Feature | Starter | Pro | Enterprise |
|------|------|------|------|
| Basic valuation | ✅ | ✅ | ✅ |
| Value range | ❌ | ✅ | ✅ |
| Market context | ❌ | ✅ | ✅ |
| Historical signals | ❌ | ✅ | ✅ |
| PDF export | ✅ (branded) | ✅ (branded) | ✅ (white-label) |
| API access | ❌ | ❌ | ✅ |
| Bulk analysis | ❌ | ❌ | ✅ |

---

## Stripe Mapping

| PixrWorth Capability | Stripe Dependency |
|------|------|
| Access control | Subscription plan |
| Usage counting | `used_docs` |
| API access | Enterprise price only |
| White-label | Enterprise metadata flag |

Stripe **does not** know PixrWorth —  
Laravel enforces PixrWorth via `plan_tier`.

---

## Laravel Enforcement Model

### Routes
```php
Route::middleware(['auth','plan:PRICE_STARTER,PRICE_PRO,PRICE_ENTERPRISE'])
  ->post('/pixrworth/evaluate');

Route::middleware(['auth','plan:PRICE_ENTERPRISE'])
  ->get('/api/pixrworth/*');
if ($user->plan_tier === 'PRICE_STARTER') {
    $user->incrementUsage('used_docs');
}


PDF Export Rules

Starter / Pro → Pixrup footer enforced

Enterprise → no footer, full white-label

Checked server-side at render time.

Internal Module Boundaries

PixrWorth does

Valuation logic

Data normalization

Confidence scoring

PixrWorth does NOT

Generate images

Modify photos

Predict renovations

Detect damage

(Those belong to PixrVision / GlowUp)

Anti-Abuse Rules

No valuation reuse across accounts

No API access without Enterprise

No hidden route access

No frontend-only checks

Summary

PixrWorth is:

The valuation brain of Pixrup

Included from Starter upward

Fully unlocked at Pro

Automatable & white-label at Enterprise

It sets the financial baseline for every other Pixrup module.

Next Module

Recommended order:

✅ PixrWorth (done)

➜ PixrVision (damage, condition, detection)

➜ PixrGlowUp (visual renovation AI)

➜ PixrSpyHunt (market & anomaly intelligence)


# PixrGlowUp — Module Definition, Pricing Impact & Stripe Enforcement

## Purpose
**PixrGlowUp** is Pixrup’s AI “renovation visualization” module.  
It takes **real property photos** and generates **photorealistic upgraded versions** (before/after) while preserving structure, layout, and perspective.

This is the “wow” module: it drives upgrades because it’s expensive (AI compute) and highly visible.

---

## What PixrGlowUp Delivers

### Core Outputs
- AI-enhanced photo (after)
- Before/After pair
- Slider-ready assets (UI)
- Metadata: style, room type, seed, prompt, model used
- Render status: pending | running | done | error

### Supported Inputs
- Interior/exterior photos (JPEG/PNG/WEBP)
- Room type (kitchen, bedroom, living room, bathroom, exterior, etc.)
- Optional style preset (modern, minimal, farmhouse, etc.)

### Supported Transform Types (MVP)
- **Enhance / Upgrade** (same layout, improved finishes)
- Light declutter (only if safe and not moving structure)
- Lighting/material improvements

> Hard rule: **no structural changes**, no furniture repositioning, no wall/door/window changes.

---

## Pricing & Plan Access

### Micro — ❌ Not Included
GlowUp requires:
- user session + dashboard
- job tracking
- expensive inference

Micro stays single one-time document flow.

---

### Starter — ❌ Not Included
Starter is for “documents + dashboard”, but **no AI renders**.

**UX rule**
- Show GlowUp card as locked
- CTA: “Upgrade to Pro to generate renders”

**Enforcement**
- Route-level block (no job creation)
- UI hidden/locked

---

### Pro — ✅ Included (Limited)
Pro unlocks GlowUp with a **monthly render cap**.

**Capabilities**
- GlowUp renders
- Style presets
- Before/After storage in project
- Add to Report (with Pixrup footer)

**Limits**
- **20 renders / month** (strict)
- Counts tracked by `used_renders`
- If exceeded → hard block + upsell to Enterprise

**Enforcement**
- `plan_tier = PRICE_PRO`
- `used_renders` monthly reset

---

### Enterprise — ✅ Included (Unlimited + White-label)
Enterprise gets full power.

**Capabilities**
- Unlimited renders (fair use)
- White-label reports using GlowUp results
- API access (optional if you expose it)
- Audit logs (optional)

**Enforcement**
- `plan_tier = PRICE_ENTERPRISE`
- no render cap
- `white_label = true` in exports

---

## Feature Matrix

| Feature | Starter | Pro | Enterprise |
|------|------|------|------|
| Generate renders | ❌ | ✅ | ✅ |
| Monthly render cap | — | 20/mo | Unlimited |
| Style presets | ❌ | ✅ | ✅ |
| Save to project history | ❌ | ✅ | ✅ |
| Add to PDF Report | ❌ | ✅ (branded) | ✅ (white-label) |
| API access | ❌ | ❌ | ✅ (optional) |

---

## Stripe Mapping
| PixrGlowUp Capability | Stripe Dependency |
|------|------|
| Unlock renders | Subscription plan |
| Render limits | DB counters (not Stripe) |
| White-label report usage | Enterprise-only flag |
| Upsell trigger | render limit reached |

Stripe only tells you “who is Pro/Enterprise”.  
Laravel enforces “how many renders”.

---

## Laravel Enforcement Model

### Recommended DB Fields
- `users.used_renders` (int, default 0)
- `users.usage_reset_at` (date/datetime)
- `users.plan_tier` (string)

---

## Enforcement Rules (Backend)

### Route Protection
- Pro+ can create jobs
- Starter cannot

```php
Route::middleware(['auth', 'plan:PRICE_PRO,PRICE_ENTERPRISE'])
  ->post('/glowup/jobs', [GlowUpController::class, 'store']);


Route::post('/glowup/jobs', ...)
  ->middleware(['auth', 'feature:ai_render']);

Rule:

Starter: always blocked

Pro: blocked when used_renders >= 20

Enterprise: always allowed

GlowUp Job Lifecycle
1) Create Job

Input: images + room_type + style preset
Output: job_id, status=queued

2) Run AI Render (async)

queue job

call provider (Replicate/OpenAI)

store outputs (S3)

3) Complete

save before_url and after_url

store prompt/seed/model for reproducibility

increment usage counters (Pro only)

Anti-Abuse Rules

Only increment used_renders when status becomes done

Idempotency key per job (avoid double charging due to retries)

Hard-limit concurrency per user (e.g. max 2 running jobs)

Rate-limit job creation endpoint

Export / PDF Rules

Pro: GlowUp results allowed, but PDF still shows “Powered by Pixrup”

Enterprise: GlowUp results allowed, PDF can remove footer

Server-side check on export pipeline.

Summary

PixrGlowUp is:

locked for Micro + Starter

Pro unlocks with 20 renders/month

Enterprise unlocks unlimited + white-label

It’s the main upgrade driver, so enforcement must be strict and impossible to bypass.