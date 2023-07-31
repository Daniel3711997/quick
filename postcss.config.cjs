const purgeCSS = require('@fullhuman/postcss-purgecss');
const purgeCSSWordpress = require('purgecss-with-wordpress');

module.exports = {
    plugins: [
        purgeCSS({
            content: ['./app/**/*', './src/**/!(*.css|*.scss)'],
            safelist: [...purgeCSSWordpress.safelist, 'custom-class'],
        }),
    ],
};
