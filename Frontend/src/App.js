import { useEffect } from "react";
import { Provider } from "react-redux";
import { BrowserRouter as Router } from "react-router-dom";
import { Flip, ToastContainer } from "react-toastify";
import { PersistGate } from "redux-persist/integration/react";
import Routes from "./routes/Routes";
import { persistor, store } from "./store/store";
import { history } from "./utils/utils";
import { LatitudeAndLongitude } from "./store/actions";


function App() {

  useEffect(() => {
    const getLocation = () => {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (position) => {
            // Extract latitude and longitude
            const { latitude, longitude } = position.coords;
            store.dispatch(LatitudeAndLongitude({ latitude, longitude }));
            // Store in localStorage
            localStorage.setItem('userLocation', JSON.stringify({ latitude, longitude }));
          },
          (error) => {
            store.dispatch(LatitudeAndLongitude({ latitude: "", longitude: "" }));
            console.error('Error getting location:', error);
          }
        );
      } else {
        store.dispatch(LatitudeAndLongitude({ latitude: "", longitude: "" }));
        console.error('Geolocation is not supported by this browser.');
      }
    };
    getLocation();
  }, []);

  return (
    <>
      <ToastContainer
        position="top-right"
        autoClose={2000}
        transition={Flip}
      // hideProgressBar={true}
      />
      <Provider store={store}>
        <PersistGate loading={null} persistor={persistor}>
          <Router history={history}>

            <Routes />
          </Router>
        </PersistGate>
      </Provider>
    </>
  );
}

export default App;
