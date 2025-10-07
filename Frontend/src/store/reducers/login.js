import { authActions } from "../constants"

const initialState = {
    loading: false,
    error: "",
    info: "",
    message: "",
    profilePic: "",
    isOutOfOffice: false,
    menuRights: [],
    token: ""
}

export const login = (state = initialState, action) => {
    switch (action.type) {
        case authActions.LOGIN:
            return {
                ...state,
                loading: true,
                data: "",
                error: "",
            }
        case authActions.LOGIN_SUCCESS:
            return {
                ...state,
                loading: false,
                info: action.payload.info,
                token: action.payload.token,
                profilePic: action.payload.profilePic,
                isOutOfOffice: action.payload.isOutOfOffice,
                error: "",
            }
        case authActions.RESET_LOGIN:
            return {
                ...state,
                loading: false,
                data: "",
                error: "",
            }
        case authActions.LOGIN_INVALID:
            return {
                ...state,
                loading: false,
                data: "",
                error: "",
                invalidCredential: action.payload
            }
        case authActions.LOGIN_ERROR:
            return {
                ...state,
                loading: false,
                data: "",
                error: action.payload
            }
        case authActions.PROFILE_PIC:
            return {
                ...state,
                profilePic: action.payload
            }
        case authActions.IS_OUT_OF_OFFICE:
            return {
                ...state,
                isOutOfOffice: action.payload
            }
        case authActions.CLEAR_LOGIN_ERROR:
            return {
                ...state,
                error: "",
            }
        case authActions.MENU_RIGHTS:
            return {
                ...state,
                menuRights: action.payload,
            }
        case authActions.LOGOUT_SUCCESS:
            return {
                ...state,
                error: "",
                data: "",
            }
        default:
            return state
    }
}