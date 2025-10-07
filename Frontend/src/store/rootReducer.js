import { combineReducers } from "redux";
import { common, login, } from "./reducers";

const appReducer = combineReducers({
  user: login,
  common: common,
});

const initialState = appReducer({}, {});

const rootReducer = (state, action) => {
  if (action.type === "LOGOUT_SUCCESS") {
    state = initialState;
  }

  return appReducer(state, action);
};

export default rootReducer;
