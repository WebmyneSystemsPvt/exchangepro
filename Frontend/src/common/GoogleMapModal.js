import React, { useState, useCallback, useEffect } from 'react';
import { GoogleMap, Marker, useJsApiLoader } from '@react-google-maps/api';

const containerStyle = {
  height: '500px'
};

const GoogleMapModal = ({ open, onClose, onSelectLocation, coordinates }) => {

  const [markerPosition, setMarkerPosition] = useState(coordinates);

  const onMapClick = useCallback((event) => {
    const { latLng } = event;
    const lat = latLng.lat();
    const lng = latLng.lng();
    setMarkerPosition({ lat, lng });
  }, [onSelectLocation]);


  useEffect(() => {
    setMarkerPosition(coordinates)
  }, [coordinates])


  const { isLoaded } = useJsApiLoader({
    id: 'google-map-script',
    googleMapsApiKey: process.env.REACT_APP_GOOGLE_CLIENTID
  })

  const [map, setMap] = React.useState(null)

  const onLoad = React.useCallback(function callback(map) {
    const bounds = new window.google.maps.LatLngBounds(markerPosition);
    map.fitBounds(bounds);

    setMap(map)
  }, [])

  const onUnmount = React.useCallback(function callback(map) {
    setMap(null)
  }, [])

  return isLoaded ? (
    <GoogleMap
      mapContainerStyle={containerStyle}
      center={markerPosition}
      onClick={onMapClick}
      zoom={15}
      onLoad={onLoad}
      onUnmount={onUnmount}
      // options={{ gestureHandling: 'none', draggable: false }}
    >
      <Marker position={markerPosition} />
    </GoogleMap>
  ) : <></>
}

export default React.memo(GoogleMapModal)
