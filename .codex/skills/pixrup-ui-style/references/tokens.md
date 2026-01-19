# Pixrup UI Tokens

## Approved Tokens and Utilities
- Containers: `npo-form-shadow`, `bg-surface`, `bg-background`, `shadow-neu-in`, `shadow-neu-out`.
- Text: `text-accent`, `text-accent/50`, `text-primary`.
- Buttons: `neu-button` (baseline for buttons).
- Neumorphic shadows: `shadow-neu-in`, `shadow-neu-out`.

## Allowed Arbitrary Values (Existing Only)
- `rounded-[12px]`
- `rounded-[16px]`
- `tracking-[0.2em]`
- `tracking-[0.3em]`
- `tracking-[0.4em]`
- `hover:scale-101`

## Card Patterns
- Standard card: `flex flex-col gap-4 rounded-[12px] bg-surface p-4 npo-form-shadow`
- Card-in-card: `flex flex-col gap-3 rounded-[12px] p-4 shadow-neu-out`
- Overview metric card: `flex w-full min-w-0 flex-col gap-3 rounded-[16px] bg-background p-4 shadow-neu-in`

## Button Pattern
- Card action button:
  `neu-button flex items-center justify-center gap-2 rounded-[12px] !bg-transparent px-4 py-4 text-sm font-medium text-accent transition-transform duration-200 ease-out hover:scale-101`

## Notes
- Do not introduce new arbitrary values without adding them here and confirming they already exist in the codebase.
- Keep class order consistent with the rules in `SKILL.md`.
