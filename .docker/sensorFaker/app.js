
const app = () => {
    console.log("Sensor Faker is running");

    const send = async ( value ) => {

        console.log( "Envoi de la valeur %s sur le sensor %s", value, 'AAA' );

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

    let value = Math.random() * 100;
    send( value );


    setInterval(() => {
        value += Math.floor( Math.random() * 5 ) - 2.5;
        send( value );
        
    }, 60000);

}

app();