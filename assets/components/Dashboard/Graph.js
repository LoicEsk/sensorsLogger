import React, { useState, useEffect, useRef } from 'react';

import Chart from "react-apexcharts";
import fr  from "apexcharts/dist/locales/fr.json";
import moment from 'moment';

import { lissageCourbe } from '../../managers/DataManager';


export const Graph = ({data, dataNames, title}) => {

    const graphRef = useRef();

    const options = {
        chart: {
            height: 350,
            type: 'area',
            zoom: {
              enabled: false
            },
            locales: [fr],
            defaultLocale: 'fr',
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            // curve: 'straight'
            curve: 'smooth'
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                opacity: 0.5
            },
        },
        xaxis: {
            type: 'datetime'
        },
        tooltip: {
            x: {
                format: "d MMM HH:mm"
            }
        }
    };

    const [ series, setSeries ] = useState( [] );
    useEffect( () => {
        const newSeries = data.map( (line, i) => {

            if( !line ) return !!series[i] && series[i];
            return {
                data: line.map( d => {
                    return {
                        x: new Date( moment(d.date).format('YYYY-MM-DDTHH:mm+00:00') ),
                        y: d.value.toFixed(2)
                    }
                }),
                name: dataNames[i] ?? 'Serie ' + i 
            };
        });
        setSeries( newSeries);

        /**
         * Lissage des courbes
         */


        // console.log( 'simplification' );
        const seriesSimplifiees =  newSeries.map( s => {
            s.data = lissageCourbe( s.data, getSimplifyInterval( s.data ) );
            return s;
        } );
        setSeries( seriesSimplifiees );

    }, [data] );

    // calcul de l'interval de simplification
    const getSimplifyInterval = ( data ) => {
        if( graphRef && graphRef.current && graphRef.current.offsetWidth ) {
            
            // interval voulu en px
            const intervalPx = 16;

            // taille en pixels
            const width = graphRef.current.offsetWidth;

            // 'taille' en ms
            const timestamps = data.map( (d) => ( d.x.getTime() ) )
            const ots = timestamps.sort();
            const min = ots.shift();
            const max = ots.pop();
            const msLine = max - min;
            
            return Math.round( msLine / width * intervalPx );

            return 360000;
        } else return 360000;
    }


    return (
        <div ref = { graphRef }>
            <Chart
                options={options}
                series={series}
                type="line"
                height={350}
                // key={ Date.now() } // pour refresh
            />
        </div>
    )
}

export default Graph;