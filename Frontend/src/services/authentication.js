
import axiosConfig from './../utils/axiosConfig';
import { login, loginSuccess } from './../store/actions';
import { ErrorToast, SuccessToast } from '../utils/Toaster';

export class AuthenticationService {
    static Login = (data) => {
        return (dispatch) => {
            dispatch(login());
            return axiosConfig
                .post("/login", data)
                .then((response) => {
                    // console.log("🚀 ~ sucess ~ response:", response)
                    // console.log("🚀 ~ AuthenticationService ~ .then ~ response:", response?.response?.data)
                    // console.log("🚀 ~ AuthenticationService ~ .then ~ response:", response.data?.responseData)
                    localStorage.removeItem("token");
                    if (response.data?.status) {
                        localStorage.setItem("token", response.data?.responseData?.access_token);
                        SuccessToast(response.data?.message)
                        dispatch(loginSuccess({
                            info: response.data?.responseData?.user,
                            profilePic: response.data?.responseData?.profilePhotoPath,
                            isOutOfOffice: response.data?.responseData?.isCOPSOutOfOffice,
                            token: true
                        }));
                        return true;
                    } else {
                        ErrorToast(response?.response?.data?.message)
                    }
                })
                .catch((error) => {
                    console.log("🚀 ~ AuthenticationService ~ return ~ error:", error)
                });
        };
    }
}