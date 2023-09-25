import { createContainer } from '@app/framework/quick';

import { Feed } from './Feed';

const renderContainer = createContainer({
    container: Feed,
    element: 'rootProfile',
});

renderContainer();
