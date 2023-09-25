import { render } from '@testing-library/react';
import { it, expect } from 'vitest';

import { App } from '@app/routes/_app';
import { Profile } from '@app/routes/Profile/Profile';

it('renders correctly', () => {
    // prettier-ignore
    const {
        container
    } = render(<App Component={Profile}/>);

    expect(container).toMatchSnapshot();
});
