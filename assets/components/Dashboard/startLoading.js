/**
 * Lance un chargement asynchrone depuis un useEffect, avec rafraîchissement optionnel,
 * et renvoie la fonction de nettoyage à retourner à React :
 *
 *     useEffect( () => startLoading( load, 180000 ), [ deps ] );
 *
 * - `load( isCancelled )` est appelé immédiatement, puis toutes les `refreshDelay` ms si fourni ;
 * - le nettoyage (démontage ou changement des dépendances) arrête l'intervalle et fait renvoyer
 *   `true` à `isCancelled()` : un chargement encore en cours doit alors ignorer son résultat
 *   (pas de setState sur un composant démonté ni de données d'un ancien widget).
 *
 * L'intervalle est créé de façon synchrone, avant la fin du premier chargement,
 * pour que le nettoyage puisse toujours l'arrêter.
 */
export const startLoading = ( load, refreshDelay = null ) => {
    let cancelled = false;
    const isCancelled = () => cancelled;

    load( isCancelled );

    const interval = refreshDelay ? setInterval( () => load( isCancelled ), refreshDelay ) : null;

    return () => {
        cancelled = true;
        if( interval !== null ) clearInterval( interval );
    };
};

export default startLoading;
