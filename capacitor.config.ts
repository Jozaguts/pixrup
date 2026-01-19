import type { CapacitorConfig } from '@capacitor/cli';

const appName = process.env.VITE_APP_NAME ?? 'Pixrup';
const appId = process.env.CAPACITOR_APP_ID ?? 'com.pixrup.app';
const serverUrl = process.env.CAPACITOR_SERVER_URL;

const config: CapacitorConfig = {
  appId,
  appName,
  webDir: 'public/build',
  bundledWebRuntime: false,
};

if (serverUrl) {
  const isHttps = serverUrl.startsWith('https://');
  config.server = {
    url: serverUrl,
    cleartext: !isHttps,
    androidScheme: isHttps ? 'https' : 'http',
  };

  try {
    const origin = new URL(serverUrl).origin;
    config.allowNavigation = [origin];
  } catch {
    // Ignore invalid URLs and keep defaults.
  }
}

export default config;
