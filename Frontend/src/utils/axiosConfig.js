import axios from "axios";
import { store } from "../store/store";
import { ErrorToast } from "./Toaster";

const axiosConfig = axios.create({
  baseURL: process.env.REACT_APP_API_URL,
});

axiosConfig.defaults.headers.common["X-Api-Key"] = process.env.REACT_APP_AUTH_KEY;


axiosConfig.interceptors.request.use(
  (request) => {
    request.headers['Authorization'] = 'Bearer ' + localStorage.getItem("token");
    // request.headers['Authorization'] = localStorage.getItem("token");
    return request;
  },
  (error) => {
    return error;
  }
);

axiosConfig.interceptors.response.use(
  (res) => res,
  (error) => {
    console.log("🚀 ~ error:", error)
    if (error?.response?.status === 404) {
      ErrorToast(error?.response.data.message)
    } else if (error?.response?.status === 401) {
      localStorage.removeItem("token");
      axiosConfig.defaults.headers.common["token"] = null;
      store.dispatch({ type: "LOGOUT_SUCCESS" });
      // window.location.href = "/home"
    }
    return error;
  }
);

// ======----======----======----======----======
// CANCEL Requests called Too Frequently
// ======----======----======----======----======
// const autoCancellationMsg = "Automatic Cancellation";
// const sourceRequest = {};
// axiosConfig.interceptors.request.use(
//   (request) => {
//     // If the application exists cancel
//     if (sourceRequest[request.url]) {
//       sourceRequest[request.url].cancel(autoCancellationMsg);
//     }
//     // Store or update application token
//     const axiosSource = axios.CancelToken.source();
//     sourceRequest[request.url] = { cancel: axiosSource.cancel };
//     request.cancelToken = axiosSource.token;
//     setTimeout(() => {
//       delete sourceRequest[request.url];
//     }, 50);
//     return request;
//   },
//   (error) => {
//     return Promise.reject(error);
//   }
// );

// ======----======----======----======----======
// Common Error Handler
// ======----======----======----======----======

// axiosConfig.interceptors.response.use(
//   (response) => response,
//   (error) => {
//     if (error.message === autoCancellationMsg) {
//       // console.log("Common Error Handler", error);
//       throw Error(1);
//     } else throw error;
//   }
// );

export default axiosConfig;
