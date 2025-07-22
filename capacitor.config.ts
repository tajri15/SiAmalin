import type { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
  appId: 'id.my.undip.siamalin',
  appName: 'SiAmalin',
  webDir: 'public/build',
  server: {
    url: 'https://siamalin-undip.my.id',
    cleartext: true
  }
};

export default config;