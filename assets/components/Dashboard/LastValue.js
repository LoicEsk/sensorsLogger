import React, { useState, useEffect } from 'react';

import moment from 'moment';

export const LastValue = ( { data, name, className } ) => {

    const [ value, setValue ] = useState( '--' );
    const [ date, setDate ] = useState( '--/--/-- --:--');

    useEffect( () => {
        if( !!data ){
            data.sort( (a, b) => {
                const dateA = moment(a.date);
                const dateB = moment(b.date);
                return dateB.unix() - dateA.unix();
            } );
            const lastData = data.shift();
            if( !!lastData ) {
                setValue( lastData.value.toFixed(2) );
                setDate( moment( lastData.date ).format( 'DD/MM/YYYY HH:mm') );
            }

        }


    }, [data] );

    return (
        <div className={"last-value " + className }>
            <p className="m-0 last-value--small">{ name }</p>
            <p className="last-value--big m-0  text-center">{ value }</p>
            <p className="m-0 last-value--small text-end">{ date }</p>
        </div>
    )
}

export default LastValue;