---
name: moai-domain-mobile-app
description: "Capacitor 6.x enterprise mobile app development for Laravel + Vue + Inertia stacks, native bridge integration, offline storage, plugins, testing, and iOS/Android release pipelines. Use when building, refactoring, or reviewing Capacitor apps, native plugins, mobile deployment, or performance/offline strategies."
---

# moai-domain-mobile-app

Enterprise Capacitor mobile development.

## Quick reference

- Use Capacitor 6.x with a web framework (Vue/React/Angular) and Vite or similar bundler.
- Keep web app in `src/` and native shells in `ios/` and `android/`.
- Prefer TypeScript for app code and plugin interfaces.
- For Laravel + Vue + Inertia, pick a delivery mode: hosted web wrapper or dedicated mobile build.

Key commands:

```bash
npm install @capacitor/core @capacitor/cli
npx cap init MyApp com.example.myapp
npx cap add ios
npx cap add android
npx cap sync
npx cap open ios
npx cap open android
```

## Core patterns

### Configure Capacitor

Use a minimal config and avoid platform-specific overrides unless needed.

```ts
import type { CapacitorConfig } from "@capacitor/cli";

const config: CapacitorConfig = {
  appId: "com.example.myapp",
  appName: "MyApp",
  webDir: "dist",
  server: {
    androidScheme: "https",
  },
};

export default config;
```

### Laravel + Vue + Inertia integration

Choose one of these and keep it consistent across environments.

**Option A: Hosted web wrapper (fastest)**  
Use the existing Laravel + Inertia app and point the WebView to it. This is a thin shell with no offline support.

```ts
import type { CapacitorConfig } from "@capacitor/cli";

const config: CapacitorConfig = {
  appId: "com.example.myapp",
  appName: "MyApp",
  webDir: "dist",
  server: {
    url: "https://app.example.com",
    cleartext: false,
  },
};

export default config;
```

Notes:
- Ensure auth cookies work in a WebView (SameSite, secure, domain).
- Use `allowNavigation` if API or auth flows redirect across domains.

**Option B: Dedicated mobile build (recommended)**  
Build a static mobile shell and call Laravel APIs directly; use token auth.

- Create a mobile entry (e.g., `resources/mobile/index.html`) with Vue router.
- Build to a dedicated output (e.g., `public/mobile`) and set `webDir` to that folder.
- Use Laravel Sanctum token auth or Passport for mobile logins.
- Configure CORS for API calls from the app.

### Use plugins safely

Guard native-only calls and fail gracefully on web.

```ts
import { Capacitor } from "@capacitor/core";
import { Preferences } from "@capacitor/preferences";

export async function saveToken(token: string) {
  if (!Capacitor.isNativePlatform()) {
    localStorage.setItem("token", token);
    return;
  }

  await Preferences.set({ key: "token", value: token });
}
```

### Handle offline data

Cache responses locally and sync when online.

```ts
import { Network } from "@capacitor/network";
import { Filesystem, Directory } from "@capacitor/filesystem";

const cachePath = "cache/products.json";

export async function loadProducts() {
  const status = await Network.getStatus();
  if (!status.connected) {
    const cached = await Filesystem.readFile({
      path: cachePath,
      directory: Directory.Data,
    });
    return JSON.parse(cached.data);
  }

  const response = await fetch("/api/products");
  const data = await response.json();
  await Filesystem.writeFile({
    path: cachePath,
    directory: Directory.Data,
    data: JSON.stringify(data),
  });
  return data;
}
```

### Build native plugins

Expose a typed interface and separate web/native implementations.

```ts
import { registerPlugin } from "@capacitor/core";

export interface DeviceVaultPlugin {
  isAvailable(): Promise<{ available: boolean }>;
  open(): Promise<void>;
}

export const DeviceVault = registerPlugin<DeviceVaultPlugin>("DeviceVault", {
  web: () => import("./web").then((m) => new m.DeviceVaultWeb()),
});
```

## Testing and release

- Use Vitest or Jest for web unit tests.
- Use Playwright or Cypress for web E2E.
- Run device smoke tests with Xcode/Android Studio for each release.
- Use `npm run build` then `npx cap sync` before opening native projects.
- Use fastlane or CI to handle signing, versioning, and store uploads.

## Workflow

1. Build web assets.
2. Sync to native shells.
3. Validate permissions and native settings.
4. Test on device.
5. Archive and submit.
