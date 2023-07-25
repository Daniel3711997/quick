import viteLogo from 'assets/vite-logo.png';

import { homeContainer } from './Home.module.scss';

export const Home = () => {
    return (
        <div className={homeContainer}>
            <h1>Hello World (Home)</h1>

            <img src={viteLogo} alt="Vite Logo" width="100" height="100" />
        </div>
    );
};
