import path from 'node:path';

import react from '@vitejs/plugin-react';
import tsconfigPaths from 'vite-tsconfig-paths';
import { defineConfig } from 'vitest/config';

export default defineConfig({
    test: {
        environment: 'jsdom',
    },
    plugins: [react(), tsconfigPaths()],
    resolve: {
        alias: {
            assets: path.resolve(__dirname, 'src/assets'),
        },
    },
});
