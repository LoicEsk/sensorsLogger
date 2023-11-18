import React, { useState, useEffect } from 'react';

import ApiInterface from '../../managers/ApiInterface';
import moment from 'moment';

import Graph from '../Dashboard/Graph';
import Loader from '../Loader';

export const WidgetGraph = ({ widget }) => {

    const { sensors, periodeValue, periodeUnit } = widget;

    const [ error, setError ] = useState( false );
    const [ isLoading, setIsLoading ] = useState( true );
    
    // chargement des data sensors
    useEffect( async () => {
        await loadSensorsData();

        // auto refresh
        const interval = setInterval( () => {
            console.log('reload');
            loadSensorsData();
        }, 6000);

        return () => clearInterval( interval );

    }, [] );

    const [ sensorsData, setSensorsData ] = useState( [] );

    const loadSensorsData = async () => {
        console.log( "Chargement des données du widget %s", widget.name );

        const newSensorsData = sensors && await Promise.all( sensors.map( async (s) => {
            setError( false ); // reset error

            const to = moment();
            const from = to.clone();
            from.subtract( periodeValue, periodeUnit );

            const api = new ApiInterface();
            const repSensorData = await api.get( '/api/sensor/' + s.id + "/data", {
                from: from.format(),
                to: to.format()
            } );

            const { status, data } = repSensorData;
            if( status === 200 && !!data ) {
                return data;
            }  else {
                setError( 'Oups, Impossible de lire des données capteur' );
                return null;
            }
        }));
        setSensorsData( newSensorsData );
        setIsLoading( false );
    }
    
    if( isLoading ) return <Loader/>;

    return (
        <>
            { !!error && <Alert variant="danger">{ error }</Alert>}
            <div className="">
                <Graph
                    data= { sensorsData }
                    dataNames= { sensors.map( s => (s.name) )}
                />
            </div>
        </>
    )
}

export default WidgetGraph;