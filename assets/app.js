/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
// import './styles/app.css';


import React from 'react';
import ReactDOM from 'react-dom';

import './styles/app.sass';
import Dashboard from './components/Dashboard';
    
const elem = document.getElementById('appJs');
if( elem ) {
    ReactDOM.render(
        <Dashboard/>,
        elem
    );
}