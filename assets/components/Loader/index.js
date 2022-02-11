import React from 'react';

import { Spinner } from 'react-bootstrap';

export const Loader = ({ inline }) => {

    if( inline ) {
        return (
            <Spinner animation="grow" />
        )
    }

    return (
        <p className="text-center">
            <Spinner animation="grow" />
        </p>
    )
}

export default Loader;