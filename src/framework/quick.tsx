import { createRoot } from 'react-dom/client';

import { App } from 'routes/_app';

import type { ComponentType } from 'react';

interface ICreateContainer {
    container: ComponentType;
    excludeWrapper?: boolean;
    element: string | HTMLElement;
}

export const createContainer =
    ({ element, excludeWrapper = false, container: ReactAppContainer }: ICreateContainer) =>
    () => {
        const rootElement = 'string' === typeof element ? document.getElementById(element) : element;

        if (!rootElement) {
            throw new Error(
                'string' === typeof element
                    ? `The provided element id "${element}" was not found on the page`
                    : 'The provided "HTMLElement" was not found on the page',
            );
        }

        createRoot(rootElement).render(excludeWrapper ? <ReactAppContainer /> : <App Component={ReactAppContainer} />);
    };
