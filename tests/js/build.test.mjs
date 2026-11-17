/**
 * Test d'intégration de la chaîne de build (webpack.config.js + Encore + Babel + Sass + PostCSS) :
 * compile réellement les assets en production et vérifie les fichiers produits.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { execFileSync } from 'node:child_process';
import { readFileSync, existsSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const projectDir = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
const buildDir = path.join(projectDir, 'public/build');

const readBuildFile = (publicPath) => readFileSync(path.join(projectDir, 'public', publicPath), 'utf8');

// chemin du binaire "encore" lu dans le package.json d'Encore (il change selon les versions)
const encoreBin = () => {
    const encoreDir = path.join(projectDir, 'node_modules/@symfony/webpack-encore');
    const { bin } = JSON.parse(readFileSync(path.join(encoreDir, 'package.json'), 'utf8'));

    return path.join(encoreDir, typeof bin === 'string' ? bin : bin.encore);
};

test('le build de production produit le JS et le CSS de l\'entrée "app"', { timeout: 300_000 }, () => {
    execFileSync(process.execPath, [encoreBin(), 'production'], {
        cwd: projectDir,
        stdio: 'pipe',
        env: { ...process.env, NODE_ENV: 'production' },
    });

    const entrypoints = JSON.parse(readFileSync(path.join(buildDir, 'entrypoints.json'), 'utf8'));
    const app = entrypoints.entrypoints.app;
    assert.ok(app.js.length >= 1, 'au moins un fichier JS');
    assert.equal(app.css.length, 1, 'un fichier CSS');

    // fichiers versionnés (hash) en production, tous présents sur le disque
    for (const file of [...app.js, ...app.css]) {
        assert.match(file, /^\/build\/[\w-]+\.[0-9a-f]{8}\.(js|css)$/);
        assert.ok(existsSync(path.join(projectDir, 'public', file)), `${file} existe`);
    }

    // le manifest référence l'entrée pour le WebpackEncoreBundle
    const manifest = JSON.parse(readFileSync(path.join(buildDir, 'manifest.json'), 'utf8'));
    assert.equal(manifest['build/app.css'], app.css[0]);

    // Sass compilé : styles du projet + Bootstrap importé depuis node_modules
    const css = readBuildFile(app.css[0]);
    assert.match(css, /\.last-value--big\{font-size:45px\}/);
    assert.match(css, /\.btn-primary\{/);

    // le code applicatif est bien embarqué (un import introuvable, ex. "qs", fait échouer le build plus haut)
    const js = app.js.map(readBuildFile).join('\n');
    assert.match(js, /getElementById\("appJs"\)/);
});
