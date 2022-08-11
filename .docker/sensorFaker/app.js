
const app = () => {
    console.log("Sensor Faker");

    const send = async ( value ) => {

        const data = new FormData();
        data.append( 'sensor', 'AAA' );
        data.append( 'value', value );
        try {
            const rep = await fetch( 'http://sf/api/sensor', {
                method: 'POST',
                body: data
            } );

            if( rep.ok ) {
                console.log( await rep.text() );
            } else {
                console.log("HTTP-Error: " + response.status);
            }

        } catch (e) {
            console.log( e );
            return null;
        }

    }


    setInterval(() => {
        const value = Math.floor( Math.random() * 100 );
        send( value );
        
    }, 60000);

}

app();