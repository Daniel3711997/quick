import { createContainer } from '@app/framework/quick';

import { Profile } from './Profile';

const renderContainer = createContainer({
    container: Profile,
    element: 'rootProfile',
});

renderContainer();
