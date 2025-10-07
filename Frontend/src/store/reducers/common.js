import { commonActions } from "../constants"

const initialState = {
    ResetSearch: false,
    OpenLoginModal: { reg: false, modal: false },
    Coordinates: { latitude: "", longitude: "" }
}

export const common = (state = initialState, action) => {
    switch (action.type) {
        case commonActions.SEARCH_PROP:
            return {
                ...state,
                ResetSearch: action.payload,
            }
        case commonActions.LAT_LONG:
            return {
                ...state,
                Coordinates: action.payload,
            }
        case commonActions.OPEN_LOGIN:
            return {
                ...state,
                OpenLoginModal: action.payload,
            }
        default:
            return state
    }
}