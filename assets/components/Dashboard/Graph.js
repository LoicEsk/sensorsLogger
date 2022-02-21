import React, { useState, useEffect } from 'react';

import Chart from "react-apexcharts";
import moment from 'moment';


export const Graph = ({data}) => {

    const options = {
        // xaxis: {
        //     type: 'datetime'
        // }
        stroke: {
            curve: 'smooth',
        }
    };

    const [ series, setSeries ] = useState( [] );
    useEffect( () => {
        const newSeries = data.map( line => {
            return {
                data: line.map( d => {
                    return {
                        x: moment(d.date).unix(),
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
        />
    )
}

export default Graph;