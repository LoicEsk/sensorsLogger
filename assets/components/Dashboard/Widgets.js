import React, { useState, useEffect } from 'react';

import ApiInterface from '../../managers/ApiInterface';
import Loader from '../Loader';


export const Widgets = () => {

    const [ isLoading, setIsLoading ] = useState( true );
    useEffect( async () => {
        const api = new ApiInterface();
        const widgetList = await api. get( '/api/widget' );
        console.log( widgetList );
        setIsLoading( false );
    }, [] );
    return (
        <>
            { isLoading ? (
                <Loader/>
            ) : (
                <p>En dev</p>
            )}
        </>
    )
}

export default Widgets;