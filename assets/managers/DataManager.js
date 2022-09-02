import simplify from 'simplify-js';

export const lissageCourbe = ( data ) => {

    const dataPoints = data.map( (p) => { 
        const { x, y } = p;
        return {
            x: x.getTime(),
            y: parseInt(y)
        };
    });

    return simplify( dataPoints, 100, false );
}