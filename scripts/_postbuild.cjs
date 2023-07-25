/* eslint-disable import/order */
/* eslint-disable no-useless-escape */
/* eslint-disable @typescript-eslint/no-var-requires */

const fs = require('node:fs');
const chalk = require('chalk');
const path = require('node:path');

fs.writeFileSync(
    path.join(__dirname, '..', 'build', '.htaccess'),
    `
Options -Indexes

<IfModule mod_authz_host.c>
    Require all granted
</IfModule>

<IfModule !mod_authz_host.c>
    Order Allow,Deny
    Allow from all
</IfModule>

SetOutputFilter DEFLATE
SetEnvIfNoCase Request_URI "\.(?:gif|jpe?g|png)$" no-gzip

<IfModule mod_headers.c>
    # Serve gzip compressed CSS and JS files if they exist
    # and the client accepts gzip.
    RewriteCond "%{HTTP:Accept-encoding}" "gzip"
    RewriteCond "%{REQUEST_FILENAME}\.gz" -s
    RewriteRule "^(.*)\.(css|js)"         "$1\.$2\.gz" [QSA]

    # Serve correct content types, and prevent mod_deflate double gzip.
    RewriteRule "\.css\.gz$" "-" [T=text/css,E=no-gzip:1]
    RewriteRule "\.js\.gz$"  "-" [T=text/javascript,E=no-gzip:1]


    <FilesMatch "(\.js\.gz|\.css\.gz)$">
      # Serve correct encoding type.
      Header append Content-Encoding gzip

      # Force proxies to cache gzipped &
      # non-gzipped css/js files separately.
      Header append Vary Accept-Encoding
    </FilesMatch>
</IfModule>

# Cache static files for a year because they have a hash in their name
<IfModule mod_headers.c>
    <FilesMatch "\.(js|css|js\.gz|css\.gz)$">
        Header set Cache-Control "public, max-age=31536000, immutable"
    </FilesMatch>

    <FilesMatch "\.(jpe?g|png|gif|svg|otf|ttf|woff|woff2)$">
        Header set Cache-Control "public, max-age=31536000, immutable"
    </FilesMatch>
</IfModule>
`,
);

console.log();
console.log(chalk.magenta('Project built successfully!'));
