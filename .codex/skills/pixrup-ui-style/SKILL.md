---
name: pixrup-ui-style
description: Enforces Pixrup UI conventions for Vue 3 + TypeScript + Inertia + Tailwind in this repo. Use when creating, refactoring, or reviewing UI components, cards, buttons, menus, tooltips, or Tailwind class patterns to apply neumorphic tokens, class ordering, cn() usage, data-slot conventions, and component extraction rules.
---

# Pixrup UI Style

## Overview
Apply the Pixrup UI style system for this project and keep UI code consistent with existing tokens, composition rules, and component patterns.

## Hard Rules
- Use Vue 3 Composition API + TypeScript only (`<script setup lang="ts">`).
- Use Tailwind utilities only. No inline styles. No new CSS unless a named utility already exists (e.g., `npo-form-shadow`, `neu-button`, `shadow-neu-*`).
- Use `cn()` from `@/lib/utils` to merge classes; do not concatenate strings manually for conditional classes.
- Only use arbitrary values that already exist in the codebase and are listed in `references/tokens.md`.
- Prefer reusable primitives under `@/components/ui/*` and compose from there. If a class string repeats 2+ times, extract a component.

## Class Composition Order
Always order classes roughly as:
layout (flex/grid/position) >
sizing (w/h/min/max) >
spacing (p/m/gap) >
typography (text/font/tracking/leading/uppercase) >
colors (bg/text/border/ring) >
borders/radius >
effects (shadow/transition/transform) >
state (hover/focus/disabled/aria)

## Component Conventions
- Use `data-slot` attributes for UI primitives.
- Keep components small and single-responsibility.
- Type props and emits; avoid `any`.
- Prefer `inheritAttrs: false` and explicit forwarding when wrapping primitives.

## Inertia Conventions
- Pages live in `resources/js/pages`, layouts in `resources/js/layouts`.
- Keep UI concerns in components; do not fetch in leaf components unless a pattern already exists.

## References
- `references/tokens.md` for approved tokens and patterns.
- `references/components.md` for canonical component patterns.
