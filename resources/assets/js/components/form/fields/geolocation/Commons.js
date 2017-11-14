function dd2dms(D, lng){
    let dir = D<0?lng?'W':'S':lng?'E':'N',
        deg = 0|(D<0?D=-D:D),
        min = 0|D%1*60,
        sec = (0|D*60%1*6000)/100;

    return `${deg}°${min}"${sec}' ${dir}`;
}

export default {
    computed: {
        defaultMapOptions:() => ({
            mapTypeControl:false,
            streetViewControl:false
        }),
    },
    methods: {
        latLng2DMS(latLng) {
            let pos = this.isLatLngInstance(latLng) ? latLng.toJSON() : latLng;
            return {
                lat: dd2dms(pos.lat),
                lng: dd2dms(pos.lng, true)
            }
        },
        isLatLngInstance(latLng) {
            return latLng instanceof google.maps.LatLng;
        }
    }
}