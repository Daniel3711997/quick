import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
import { type ComponentType, StrictMode } from 'react';

import { RequestBoundary } from 'components/RequestBoundary';

interface IAppProps {
    Component: ComponentType;
}

const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            staleTime: 1000 * 60 * 20, // 20 minutes
            cacheTime: 1000 * 60 * 60 * 2, // 2 hours
        },
    },
});

export const App = ({ Component, ...pageProps }: IAppProps) => {
    return (
        <StrictMode>
            <QueryClientProvider client={queryClient}>
                <ReactQueryDevtools initialIsOpen={false} />

                <RequestBoundary
                    fallback={({ resetErrorBoundary }) => (
                        <div>
                            <p>Something went wrong</p>
                            <button onClick={() => resetErrorBoundary()}>Try again</button>
                        </div>
                    )}
                >
                    <Component {...pageProps} />
                </RequestBoundary>
            </QueryClientProvider>
        </StrictMode>
    );
};
