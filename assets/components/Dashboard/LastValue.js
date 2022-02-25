import React, { useState, useEffect } from 'react';

import moment from 'moment';

export const LastValue = ( { data, name, className } ) => {

    const [ value, setValue ] = useState( '--' );
    const [ date, setDate ] = useState( '--/--/-- --:--');

    useEffect( () => {
        data.sort( (a, b) => {
            const dateA = moment(a.date);
            const dateB = moment(b.date);
            return dateB.unix() - dateA.unix();
        } );
        const lastData = data.shift();
        setValue( lastData.value );
        setDate( moment( lastData.date ).format( 'DD/MM/YYYY hh:mm') );

    }, [data] );

    return (
        <div className={"text-center " + className }>
            <p className="display-4 m-0">{ value }</p>
            <p className="m-0"><small>{ date }</small></p>
        </div>
    )
}

export default LastValue;