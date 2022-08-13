import React from 'react';

import { Spinner } from 'react-bootstrap';

export const Loader = ({ inline }) => {

    if( inline ) {
        return (
            <Spinner animation="grow" />
        )
    }

    return (
        <div className="text-center">
            <Spinner animation="grow" />
        </div>
    )
}

export default Loader;