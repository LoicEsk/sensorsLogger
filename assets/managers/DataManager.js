import simplify from 'simplify-js';
import moment from 'moment';

export const lissageCourbe = ( data, intervalFilter = 7200000 ) => {

    const dataPoints = data.map( (p) => { 
        const { x, y } = p;
        return {
            x: x.getTime(),
            y: parseInt(y)
        };
    });

    // converion de format pour le traitements
    const oData = data
        .map( d => {
            const { x, y } = d;
            return {
                x: x.getTime(),
                y: parseFloat(y)
            }
        } )
        .sort( (a, b) => {
            const { x: aX } = a;
            const { x: bX } = b;
            return aX - bX;
    });

    const startData = oData.shift();
    const endData   = oData.pop();

    console.log( 'startData = ', startData );
    console.log( 'endData = ', endData );

    // nouveau tableau de valeurs
    const newData = [];

    let periodStart = startData.x;
    let periodEnd   = periodStart + intervalFilter;

    newData.push( startData ); // première valeur conservée
    
    let pt = oData.shift();
    while( periodStart <= endData.x ) {
        console.log('Période');
        periodEnd   = periodStart + intervalFilter;
        
        // liste des points dans l'interval
        const pts = [];
        while( !!pt && pt.x <= periodEnd ) {
            pts.push( pt );
            pt = oData.shift();
        }
        
        // point moyen
        if( pts.length !== 0) {
            console.log( 'Reduction de ', pts );
            const x = pts.reduce( (a, b) => (a + b.x), 0) / pts.length;
            const y = pts.reduce( (a, b) => (a + b.y), 0 ) / pts.length;
            console.log( '-> x: %s, y: %s', x, y);
            newData.push({
                x: x,
                y: y
            });
        }


        periodStart = periodEnd;

    }

    newData.push( endData ); // dernière valeur conservée

    // retour au format inital
    const output = newData.map( d => {
        return {
            x: new Date( d.x ),
            y: d.y.toString()
        }
    });

    console.log( 'entrée : ', data );
    console.log( 'sortie : ', output );

    return output;

}