import React, { useState, useEffect } from 'react';

import Chart from "react-apexcharts";
import moment from 'moment';


export const Graph = ({data, title}) => {

    const options = {
        chart: {
            height: 350,
            type: 'line',
            zoom: {
              enabled: false
            }
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
        }  
    };

    const [ series, setSeries ] = useState( [] );
    useEffect( () => {
        const newSeries = data.map( line => {
            return {
                data: line.map( d => {
                    return {
                        x: moment(d.date),
                        y: d.value
                    }
                })
            };
        });
        console.log( newSeries );
        setSeries( newSeries);
    }, [data] );


    return (
        <Chart
            options={options}
            series={series}
            type="line"
            height={350}
        />
    )
}

export default Graph;