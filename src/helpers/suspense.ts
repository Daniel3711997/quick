export const loadSuspense = () => {
    setTimeout(() => {
        const frameworkLoader = document.querySelectorAll('.quick-framework-loader');

        if (frameworkLoader.length) {
            frameworkLoader.forEach(loader => {
                loader.classList.add('active');
            });
        }
    }, 250);
};
