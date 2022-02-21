import axios from 'axios';
import qs from 'qs';


export class ApiInterface {

    userToken = '';
    apiUrl = '';

    constructor() {
        this.apiUrl = window.location.origin;
    }

    async get( route, params) {
        return this.request( 'get', route, params);
    }

    async post( route, params, data) {
        return this.request( 'post', route, params, data);
    }

    async put( route, data) {
        return this.request( 'put', route, data);
    }

    async delete( route, params, data) {
        return this.request( 'delete', route, params, data);
    }

    async request( methode, route, params = [], data = []) {
        const routeRoot = route.match( /^\// ) ? route : '/' + route;
        const url = this.apiUrl + routeRoot;

        try {
            const rep = await axios( {
                method: methode,
                url: url,
                params: params,
                data: qs.stringify( data )
            });

            return rep;

        } catch( error ) {
            // console.log( error );
            if( error.response ) return { data: error.response.data, status: error.response.status };
            else return { data: null, status: 499 }; // Quand le requête est abandonnée par le client
        }
    }
}

export default ApiInterface;