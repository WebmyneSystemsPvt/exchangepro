import React from "react";
import { Route, Redirect, useLocation } from "react-router-dom";

function PrivateRoute({ component: Component, ...rest }) {

  const location = useLocation();

  return (
    <Route
      {...rest}
      render={(props) =>
        localStorage.getItem("token") ? (
          <Component {...props} />
        ) : (
          <Redirect
            to={{ pathname: "/home", state: { from: location } }}
          />
        )
      }
    />
  );
}

export default PrivateRoute;
