import { authActions } from "../constants"

export const login = () => {
    return {
        type: authActions.LOGIN
    }
}

export const resetLogin = () => {
    return {
        type: authActions.RESET_LOGIN
    }
}

export const loginSuccess = (data) => {
    return {
        type: authActions.LOGIN_SUCCESS,
        payload: data
    }
}

export const loginInvalid = (data) => {
    return {
        type: authActions.LOGIN_INVALID,
        payload: data
    }
}

export const loginError = (error) => {
    return {
        type: authActions.LOGIN_ERROR,
        payload: error
    }
}

export const clearLoginError = () => {
    return {
        type: authActions.CLEAR_LOGIN_ERROR
    }
}

export const logoutSuccess = () => {
    return {
        type: authActions.LOGOUT_SUCCESS,
    }
}

export const DefaultProfilePic = (data) => {
    return {
        type: authActions.PROFILE_PIC,
        payload: data
    }
}

export const IsOutOfOffice = (data) => {
    return {
        type: authActions.IS_OUT_OF_OFFICE,
        payload: data
    }
}

export const MenuRightList = (data) => {
    return {
        type: authActions.MENU_RIGHTS,
        payload: data
    }
}