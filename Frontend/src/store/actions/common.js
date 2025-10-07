import { commonActions } from "../constants"

export const SearchProperties = (data) => {
    return {
        type: commonActions.SEARCH_PROP,
        payload: data
    }
}

export const LatitudeAndLongitude = (data) => {
    return {
        type: commonActions.LAT_LONG,
        payload: data
    }
}

export const OpenLogin = (data) => {
    return {
        type: commonActions.OPEN_LOGIN,
        payload: data
    }
}