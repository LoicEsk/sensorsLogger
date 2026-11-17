/**
 * Tests de ApiInterface avec le vrai client axios contre un serveur HTTP local :
 * vérifie le contrat utilisé par le tableau de bord après les mises à jour d'axios et de qs.
 */
import { test, before, after } from 'node:test';
import assert from 'node:assert/strict';
import http from 'node:http';

import ApiInterface from '../../assets/managers/ApiInterface.js';

let server;
let origin;
const received = [];

before(async () => {
    server = http.createServer((req, res) => {
        let body = '';
        req.on('data', (chunk) => { body += chunk; });
        req.on('end', () => {
            received.push({
                method: req.method,
                url: req.url,
                contentType: req.headers['content-type'],
                body,
            });

            if (req.url.startsWith('/missing')) {
                res.writeHead(404, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ error: 'not found' }));
                return;
            }

            res.writeHead(200, { 'Content-Type': 'application/json' });
            res.end(JSON.stringify([{ value: 21.5 }]));
        });
    });
    await new Promise((resolve) => server.listen(0, '127.0.0.1', resolve));
    origin = `http://127.0.0.1:${server.address().port}`;

    // ApiInterface cible l'origine de la page courante
    globalThis.window = { location: { origin } };
});

after(async () => {
    await new Promise((resolve) => server.close(resolve));
    delete globalThis.window;
});

const lastRequest = () => received[received.length - 1];

test('get() appelle la route sur l\'origine de la page avec les paramètres en query string', async () => {
    const api = new ApiInterface();

    const rep = await api.get('/api/sensor/3/data', { from: '2024-01-10T00:00:00+01:00', to: '2024-01-11' });

    assert.equal(rep.status, 200);
    assert.deepEqual(rep.data, [{ value: 21.5 }]);
    assert.equal(lastRequest().method, 'GET');
    assert.equal(lastRequest().url, '/api/sensor/3/data?from=2024-01-10T00:00:00%2B01:00&to=2024-01-11');
});

test('une route sans slash initial est préfixée', async () => {
    const api = new ApiInterface();

    await api.get('api/widget');

    assert.equal(lastRequest().url, '/api/widget');
});

test('post() envoie les données encodées en application/x-www-form-urlencoded', async () => {
    const api = new ApiInterface();

    await api.post('/api/sensor', {}, { sensor: 'AAA', value: 21.5, tags: ['a', 'b'] });

    assert.equal(lastRequest().method, 'POST');
    assert.match(lastRequest().contentType, /^application\/x-www-form-urlencoded/);
    assert.equal(decodeURIComponent(lastRequest().body), 'sensor=AAA&value=21.5&tags[0]=a&tags[1]=b');
});

test('une réponse en erreur est renvoyée sans lever d\'exception', async () => {
    const api = new ApiInterface();

    const rep = await api.get('/missing');

    assert.deepEqual(rep, { data: { error: 'not found' }, status: 404 });
});

test('un serveur injoignable donne le statut 499 et aucune donnée', async () => {
    const api = new ApiInterface();
    // port 1 : connexion refusée immédiatement
    api.apiUrl = 'http://127.0.0.1:1';

    const rep = await api.get('/api/widget');

    assert.deepEqual(rep, { data: null, status: 499 });
});
