import React, { useState, useEffect} from 'react';

import { Col, Alert } from 'react-bootstrap';
import moment from 'moment';

import ApiInterface from '../../managers/ApiInterface';

import Graph from './Graph';


export const Widget = ({ widget }) => {
    const { sensors, periodeValue, periodeUnit } = widget;

    const colSizeMapping = {
        1: 12,
        2: 6,
        3: 4
    }
    const colSize = colSizeMapping[widget.size];

    const [ to, setTo ] = useState( moment() );

    const [ error, setError ] = useState( false );

    // chargement des data sensors
    useEffect( async () => {
        await loadSensorsData();
    }, [widget] );
    
    const [ sensorsData, setSensorsData ] = useState( [] );
    const loadSensorsData = async () => {
        const newSensorsData = sensors && await Promise.all( sensors.map( async (s) => {
            setError( false ); // reset error

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
            }
        }));
        setSensorsData( newSensorsData );


    }


    return (
        <Col md={ colSize } className={ "widget widget__size-" + widget.size } >
            { !!error && <Alert variant="danger">{ error }</Alert>}
            <p className="widget--title">{ widget.name }</p>
            <Graph
                data= { sensorsData }
            />
        </Col>
    )
}

export default Widget;