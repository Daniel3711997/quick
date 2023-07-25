import { createContainer } from 'framework/quick';

import { Feed } from './Feed';

const renderContainer = createContainer({
    container: Feed,
    element: 'rootProfile',
});

renderContainer();
