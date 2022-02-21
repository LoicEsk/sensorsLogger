import React, { useState, useEffect } from 'react';

import { Row, Alert } from 'react-bootstrap';

import ApiInterface from '../../managers/ApiInterface';
import Loader from '../Loader';
import Widget from './Widget';


export const Widgets = () => {

    const [ error, setError ] = useState( false );

    const [ isLoading, setIsLoading ] = useState( true );
    const [ widgets, setWidgets ] = useState( [] );
    const [ sensors, setSensors ] = useState( [] );

    useEffect( async () => {
        const api = new ApiInterface();
        const widgetList = await api. get( '/api/widget' );
        const { status, data } = widgetList;

        // Traitement des données widgets
        if( status === 200 && !!data && Array.isArray( data ) ) {
            
            // store pour les widgets
            const newWidgets = data.map( d => {
                d.isLoading = false;
                return d;
            } );
            setWidgets( newWidgets );

        } else {
            setError( 'Oups, il y a un problème avec les widgets' );
        }


        setIsLoading( false );
    }, [] );


    return (
        <>
            { !!error && <Alert variant="danger">{ error }</Alert> }

            { isLoading ? (
                <Loader/>
            ) : (
                <Row>
                    { widgets.map( (w, i) => {
                        return (
                            <Widget widget={ w } key={ i } />
                        )
                    }) }
                </Row>
            )}
        </>
    )
}

export default Widgets;