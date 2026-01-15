# Pixrup UI Component Patterns

## 1) Menu Item Wrapper
Pattern:
- Small wrapper component
- Accepts `class?: HTMLAttributes['class']`
- Uses `cn('group/menu-item relative mb-2', props.class)`
- Sets `data-slot` and `data-sidebar` attributes

## 2) Tooltip Trigger Wrapper
Pattern:
- Wraps `reka-ui` trigger
- `v-bind="props"`
- Sets `data-slot="tooltip-trigger"`

## 3) Sidebar Menu Button with Optional Tooltip
Pattern:
- `inheritAttrs: false`
- `delegatedProps` computed to remove `tooltip`
- If no tooltip: render child directly
- If tooltip: wrap with `<Tooltip><TooltipTrigger as-child>...`
- TooltipContent: `side="right" align="center"`
- `:hidden="state !== 'collapsed' || isMobile"`

## 4) Cards
Canonical cards:
A) Standard card:
- `flex flex-col gap-4 rounded-[12px] bg-surface p-4 npo-form-shadow`

B) Card-in-card:
- `flex flex-col gap-3 rounded-[12px] p-4 shadow-neu-out`

C) Overview metric card:
- `flex w-full min-w-0 flex-col gap-3 rounded-[16px] bg-background p-4 shadow-neu-in`
- Header row: `border-b-1 border-accent/20 pb-2 text-xs font-semibold tracking-[0.2em] text-accent uppercase`
- Title uses `break-words`

## 5) Buttons
- Use `neu-button` when present.
- Example card action button baseline:
  `neu-button flex items-center justify-center gap-2 rounded-[12px] !bg-transparent px-4 py-4 text-sm font-medium text-accent transition-transform duration-200 ease-out hover:scale-101`

If this exact pattern appears multiple times, extract `CardActionButton.vue`.

## 6) Status Indicator (Badge)
Canonical:
- `inline-flex items-center rounded-sm px-4 py-2 text-xs font-medium text-accent/50 ring shadow-neu-in ring-white`

Prefer creating a `StatusBadge.vue` if used 2+ times.
