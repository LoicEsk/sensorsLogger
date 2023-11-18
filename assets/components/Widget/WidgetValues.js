import React from 'react';

import Loader from '../Loader';
import LastValue from '../Dashboard/LastValue';


export const WidgetValues = ({ widget }) => {

    const { sensors, name } = widget;

    return (
        <div className="d-flex">
            { sensors.map( (s, i) => (
                <LastValue
                sensor={s}
                key={i}
                />
                ))}
        </div>
    )
}

export default WidgetValues;