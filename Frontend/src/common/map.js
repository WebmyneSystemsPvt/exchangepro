// import React, { useState, useCallback } from 'react';
// import { Modal, Box, Typography, Button } from '@mui/material';
// import { GoogleMap, LoadScript, Marker } from '@react-google-maps/api';

// const containerStyle = {
//   width: '100%',
//   height: '600px',
// };

// const center = {
//   lat: 22.26,
//   lng: 73.2162,
// };

// const GoogleMapModal = ({ open, onClose, onSelectLocation, coordinates }) => {
// const [markerPosition, setMarkerPosition] = useState(center);

// const onMapClick = useCallback((event) => {
//   const { latLng } = event;
//   const lat = latLng.lat();
//   const lng = latLng.lng();
//   setMarkerPosition({ lat, lng });
//   onSelectLocation({ lat, lng });
// }, [onSelectLocation]);

//   return (
//     // <LoadScript googleMapsApiKey={process.env.REACT_APP_GOOGLE_CLIENTID}>
//     // <LoadScript googleMapsApiKey="AIzaSyDhducne7FwnQ5y595GvVwYyxDcJzmZuuc">
//     <LoadScript
//       id="script-loader"
//       googleMapsApiKey={"AIzaSyBX1z5nvjcjzyxSMT-QCVS3ERu6Y3iNSb0"}
//       language="en"
//       region="EN"
//       version="weekly"
//     >
// <GoogleMap
//   mapContainerStyle={containerStyle}
//   center={center}
//   zoom={10}
//   onClick={onMapClick}
// >
//   <Marker position={markerPosition} />
// </GoogleMap>
//     </LoadScript>
//     // <Modal
//     //   open={open}
//     //   onClose={onClose}
//     //   aria-labelledby="google-map-modal-title"
//     //   aria-describedby="google-map-modal-description"
//     // >
//     //   <Box
//     //     sx={{
//     //       position: 'absolute',
//     //       top: '50%',
//     //       left: '50%',
//     //       transform: 'translate(-50%, -50%)',
//     //       width: 600,
//     //       bgcolor: 'background.paper',
//     //       boxShadow: 24,
//     //       p: 4,
//     //     }}
//     //   >
//     //     <Typography id="google-map-modal-title" variant="h6" component="h2">
//     //       Select Location
//     //     </Typography>
//     //     {/* <LoadScript googleMapsApiKey="AIzaSyB-xFUZ4RDna7HtgjPxrwkX00XdW_0DyPw"> */}
//     // <LoadScript googleMapsApiKey="">
//     //   <GoogleMap
//     //     mapContainerStyle={containerStyle}
//     //     center={center}
//     //     zoom={10}
//     //     onClick={onMapClick}
//     //   >
//     //     <Marker position={markerPosition} />
//     //   </GoogleMap>
//     // </LoadScript>
//     //     <Box sx={{ mt: 2 }}>
//     //       <Button variant="contained" onClick={onClose}>Close</Button>
//     //     </Box>
//     //   </Box>
//     // </Modal>
//   );
// };

// export default GoogleMapModal;
