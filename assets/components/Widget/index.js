import React, { useState, useEffect} from 'react';

import { Col, Alert } from 'react-bootstrap';

import WidgetGraph from './WidgetGraph';
import WidgetValues from './WidgetValues';


export const Widget = ({ widget }) => {
    const { widgetType } = widget;

    const colSizeMapping = {
        1: 12,
        2: 6,
        3: 4
    }
    const colSize = colSizeMapping[widget.size];

    
    const getWidgetContent = (typeW) => {
        switch( typeW ) {
            case 'values': return <WidgetValues widget={widget} />
            default: return <WidgetGraph widget={ widget } />
        }
    }


    return (
        <Col md={ colSize } className={ "widget widget__size-" + widget.size } >
            <p className="widget--title">{ widget.name }</p>

            { getWidgetContent( widgetType ) }

        </Col>
    )
}

export default Widget;