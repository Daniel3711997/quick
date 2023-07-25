import { createContainer } from 'framework/quick';

import { Home } from './Home';

const renderContainer = createContainer({
    container: Home,
    element: 'rootProfile',
});

renderContainer();
