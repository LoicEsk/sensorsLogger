import React, { useState, useEffect } from 'react';

import moment from 'moment';

import ApiInterface from '../../managers/ApiInterface';

export const LastValue = ( { sensor, className } ) => {

    const [ value, setValue ] = useState( '--' );
    const [ date, setDate ] = useState( '--/--/-- --:--');

    console.debug( sensor );

    const { name, id } = sensor;

    const getMinMax = ( data ) => {
        if( Array.isArray( data ) ){
            data.sort( (a, b) => {
                const dateA = moment(a.date);
                const dateB = moment(b.date);
                return dateB.unix() - dateA.unix();
            } );

            if( data.length !== 0 ) {
                return {
                    min
                }
            }
            const lastData = data.shift();
            

        }


    };

    const loadData = () => {
        const api = new ApiInterface();

        const formtDate = moment().subtract( 1, 'h' );
        const toDate = moment();
        const resp = api.get( `/api/sensors/${id}/data`, { from: fromDate.format( 'YYYY-MM-DD'), to: toDate.format( 'YYYY-MM-DD' ) } );
    }

    useEffect( () => {  

    }, [] );

    return (
        <div className={"last-value " + className }>
            <p className="m-0 last-value--small">{ name }</p>
            <p className="last-value--big m-0  text-center">{ value }</p>
            <p className="m-0 last-value--small text-end">{ date }</p>
        </div>
    )
}

export default LastValue;