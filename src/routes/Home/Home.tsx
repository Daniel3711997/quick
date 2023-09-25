import viteLogo from '@app/assets/vite-logo.png';

import HomeStyles from './Home.module.scss';

export const Home = () => {
    return (
        <div className={HomeStyles['home-container']}>
            <h1>Hello World (Home)</h1>

            <img src={viteLogo} alt="Vite Logo" width="100" height="100" />
        </div>
    );
};
