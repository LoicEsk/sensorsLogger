/**
 * Tests de startLoading : la planification de chargement utilisée par les effets du tableau de bord
 * (Widget : chargement + rafraîchissement auto, Widgets : chargement unique).
 * Les timers sont simulés avec mock.timers de node:test.
 */
import { test, beforeEach, afterEach, mock } from 'node:test';
import assert from 'node:assert/strict';

import startLoading from '../../assets/components/Dashboard/startLoading.js';

const REFRESH_DELAY = 180000;

beforeEach(() => {
    mock.timers.enable({ apis: ['setInterval'] });
});

afterEach(() => {
    mock.timers.reset();
});

// promesse résolue « à la main », pour simuler une requête API encore en cours
const deferred = () => {
    let resolve;
    const promise = new Promise((r) => { resolve = r; });

    return { promise, resolve };
};

test('renvoie une fonction de nettoyage utilisable par useEffect (pas une Promise)', () => {
    const stop = startLoading(async () => {}, REFRESH_DELAY);

    assert.equal(typeof stop, 'function');
    stop();
});

test('lance le premier chargement immédiatement, sans annulation', () => {
    const calls = [];

    const stop = startLoading((isCancelled) => { calls.push(isCancelled()); }, REFRESH_DELAY);

    assert.deepEqual(calls, [false]);
    stop();
});

test('relance le chargement à chaque période de rafraîchissement', () => {
    let calls = 0;
    const stop = startLoading(() => { calls++; }, REFRESH_DELAY);

    mock.timers.tick(REFRESH_DELAY - 1);
    assert.equal(calls, 1);

    mock.timers.tick(1);
    assert.equal(calls, 2);

    mock.timers.tick(REFRESH_DELAY);
    assert.equal(calls, 3);

    stop();
});

test('le nettoyage arrête le rafraîchissement automatique', () => {
    let calls = 0;
    const stop = startLoading(() => { calls++; }, REFRESH_DELAY);

    stop();
    mock.timers.tick(REFRESH_DELAY * 3);

    assert.equal(calls, 1);
});

test('montages/démontages successifs (changement de widget) : seul le dernier intervalle reste actif', () => {
    const calls = { a: 0, b: 0, c: 0 };

    const stopA = startLoading(() => { calls.a++; }, REFRESH_DELAY);
    stopA();
    const stopB = startLoading(() => { calls.b++; }, REFRESH_DELAY);
    stopB();
    const stopC = startLoading(() => { calls.c++; }, REFRESH_DELAY);

    mock.timers.tick(REFRESH_DELAY * 2);

    assert.deepEqual(calls, { a: 1, b: 1, c: 3 });
    stopC();
});

test('un chargement terminé après le nettoyage voit l\'annulation et n\'applique pas son résultat', async () => {
    const request = deferred();
    const applied = [];
    let loading;

    const stop = startLoading((isCancelled) => {
        loading = (async () => {
            const data = await request.promise;
            if (isCancelled()) return;
            applied.push(data);
        })();
    }, REFRESH_DELAY);

    stop(); // démontage pendant la requête
    request.resolve('mesures');
    await loading;

    assert.deepEqual(applied, []);
});

test('un chargement terminé avant le nettoyage applique son résultat', async () => {
    const request = deferred();
    const applied = [];
    let loading;

    const stop = startLoading((isCancelled) => {
        loading = (async () => {
            const data = await request.promise;
            if (isCancelled()) return;
            applied.push(data);
        })();
    }, REFRESH_DELAY);

    request.resolve('mesures');
    await loading;

    assert.deepEqual(applied, ['mesures']);
    stop();
});

test('sans période de rafraîchissement : un seul chargement, annulable', () => {
    const calls = [];
    let isCancelledRef;

    const stop = startLoading((isCancelled) => {
        isCancelledRef = isCancelled;
        calls.push(isCancelled());
    });

    mock.timers.tick(REFRESH_DELAY * 10);
    assert.deepEqual(calls, [false]);

    stop();
    assert.equal(isCancelledRef(), true);
});
