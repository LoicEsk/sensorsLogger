/**
 * Test du simulateur de capteur (.docker/sensorFaker/app.js) : le vrai script est lancé
 * dans un processus Node, avec fetch simulé (réponse HTTP en erreur) et setInterval neutralisé
 * pour que le processus s'arrête après le premier envoi.
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { execFileSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const projectDir = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');
const script = path.join(projectDir, '.docker/sensorFaker/app.js');

// module chargé avant le script via --import
const stubs = `
globalThis.fetch = async () => new Response('erreur serveur', { status: 500 });
globalThis.setInterval = () => 0;
`;

test('une réponse HTTP en erreur est journalisée avec son statut', () => {
    const output = execFileSync(process.execPath, [
        '--import', 'data:text/javascript,' + encodeURIComponent(stubs),
        script,
    ], { encoding: 'utf8', timeout: 10_000 });

    assert.match(output, /^HTTP-Error: 500$/m);
    assert.doesNotMatch(output, /ReferenceError/);
});
