/* eslint-disable import/no-unresolved */

import { Dispose } from 'helpers/dispose';
import { loadSuspense } from 'helpers/suspense';

import 'styles/main.scss';

if ('development' !== import.meta.env.MODE) {
    // @ts-expect-error - No types
    import('vite/modulepreload-polyfill');
}

loadSuspense();

if (import.meta.hot) {
    // (HMR) Hot Module Reload - https://vitejs.dev/guide/api-hmr.html
    import.meta.hot.accept();

    // Cleanup
    import.meta.hot.dispose(() => {
        Dispose.dispose();
    });
}

console.log('Hello World (Vanilla)');
