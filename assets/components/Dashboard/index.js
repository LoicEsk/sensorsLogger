import React from 'react';

import { Container } from 'react-bootstrap';

import Widgets from './Widgets';

export const Dashboard = () => {

    return (
        <Container fluid>
            <Widgets/>
        </Container>
    )
}

export default Dashboard;