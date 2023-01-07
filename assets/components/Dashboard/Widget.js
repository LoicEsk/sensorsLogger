import React, { useState, useEffect} from 'react';

import { Col, Alert } from 'react-bootstrap';
import moment from 'moment';

import ApiInterface from '../../managers/ApiInterface';

import Graph from './Graph';
import Loader from '../Loader';
import LastValue from './LastValue';


export const Widget = ({ widget }) => {
    const { sensors, periodeValue, periodeUnit } = widget;

    const colSizeMapping = {
        1: 12,
        2: 6,
        3: 4
    }
    const colSize = colSizeMapping[widget.size];

    const [ error, setError ] = useState( false );
    const [ isLoading, setIsLoading ] = useState( true );

    // chargement des data sensors
    useEffect( async () => {
        await loadSensorsData();

        // auto refresh
        const interval = setInterval( () => {
            console.log('reload');
            loadSensorsData();
        }, 180000);

        return () => clearInterval( interval );

    }, [widget] );
    
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


    return (
        <Col md={ colSize } className={ "widget widget__size-" + widget.size } >
            { !!error && <Alert variant="danger">{ error }</Alert>}
            <p className="widget--title">{ widget.name }</p>
            

            
            { isLoading ? <Loader/> : (
                <div className="">
                    <div className="d-flex">
                        { sensorsData.map( (data, i) => (
                            <LastValue
                            data={data}
                            name={ sensors[i].name }
                            key={i}
                            />
                            ))}
                    </div>
                    <Graph
                        data= { sensorsData }
                        dataNames= { sensors.map( s => (s.name) )}
                    />
                </div>
            )}
        </Col>
    )
}

export default Widget;