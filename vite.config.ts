import fs from 'node:fs';
import path from 'node:path';

import react from '@vitejs/plugin-react';
import { defineConfig } from 'vite';
import tsconfigPaths from 'vite-tsconfig-paths';

import pckg from './package.json';
import quick from './src/routes.json';

export const isProduction = 'production' === process.env.NODE_ENV;
export const isDevelopment = 'development' === process.env.NODE_ENV;

const appPackage = pckg as typeof pckg & {
    quickApplicationPublicPath: string;
    quickApplicationNodeEnvironment: string;
};

appPackage.quickApplicationNodeEnvironment = process.env.NODE_ENV;
appPackage.quickApplicationPublicPath = '/wp-content/plugins/quick/build';

fs.writeFileSync(path.join(__dirname, 'package.json'), JSON.stringify(appPackage, null, 4));

export default defineConfig({
    resolve: {
        alias: {
            assets: path.resolve(__dirname, 'src/assets'),
        },
    },
    plugins: [react(), tsconfigPaths()],
    base: appPackage.quickApplicationPublicPath,
    server: {
        port: 4000,
        cors: true,
        host: 'localhost',
        origin: 'http://localhost:4000',
    },
    build: {
        outDir: 'build',
        manifest: true,
        rollupOptions: {
            output: {
                entryFileNames: 'app/[name]-[hash].js',
                chunkFileNames: 'app/components/[name]-[hash].js',
                assetFileNames: 'app/assets/[name]-[hash][extname]',
            },
            input: {
                ...quick.routes.reduce<Record<string, string>>((acc, route) => {
                    acc[route.entry.name] = path.join(__dirname, route.entry.path);
                    return acc;
                }, {}),
            },
        },
    },
    css: {
        modules: {
            // localsConvention: 'camelCaseOnly',
            generateScopedName: isProduction ? '[hash:base64:12]' : '[name]__[local]--[hash:base64:12]',
        },
    },
});
