import type { Runtime, Server } from './runtime';

declare global {
    interface Window {
        quickServer: Server;
        quickRuntime: Runtime;
    }

    namespace NodeJS {
        interface ProcessEnv {
            readonly NODE_ENV: 'development' | 'production';
        }
    }
}
