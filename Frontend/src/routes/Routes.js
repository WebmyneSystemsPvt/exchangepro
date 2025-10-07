import { useMemo, useRef } from "react";
import { Switch, Route, Redirect, useLocation } from "react-router-dom";
import NotFoundPage from "./NotFoundPage";
import {
    publicRoutes as allPublicRoutes,
    protectedRoutes as allProtectedRoutes,
} from "./allRoutes";
import PrivateRoute from "./PrivateRoute";
import Header from './../common/Header';
import ErrorBoundary from "../_components/ErrorBoundary";
import ProfileMenu from './../_components/Profile/ProfileMenu';
import Footer from "../common/Footer";
import useScrollToTop from "../useScrollToTop";

function Routes() {
    const currentLocation = useLocation();

    // const { menuRights } = useSelector((state) => ({ menuRights: state.user?.menuRights, }));

    const pageContainerRef = useRef();

    const publicRoutesMap = useMemo(
        () =>
            allPublicRoutes.map(({ Component, title, path, ...rest }) => {
                return (
                    <Route
                        key={path}
                        exact
                        path={path}
                        render={() =>
                            rest?.header ? (
                                <>
                                    <Header />
                                    {!rest?.hideProfile && <ProfileMenu />}
                                    <Component />
                                    {!rest?.hideFooter && <Footer />}
                                </>
                            ) : (
                                <>
                                    {!rest?.hideProfile && <ProfileMenu />}
                                    <Component />
                                    {!rest?.hideFooter && <Footer />}
                                </>
                            )
                        }
                        title={title}
                    />
                );
            }),
        []
    );

    const privateRoutesMap = useMemo(
        () =>
            allProtectedRoutes.map(({ Component, title, path, ...rest }) => {
                return (
                    <PrivateRoute
                        exact
                        key={path}
                        path={path}
                        component={() =>
                            rest?.header ? (
                                <>
                                    {/* <MarqueeSlider /> */}
                                    <Header />
                                    {!rest?.hideProfile && <ProfileMenu />}
                                    <Component />
                                    <Footer />
                                </>
                            ) : (
                                <>
                                    {!rest?.hideProfile && <ProfileMenu />}
                                    <Component />
                                    <Footer />
                                </>
                            )
                        }
                        title={title}
                    />
                );
            }),
        []
    );

    useScrollToTop();


    return (
        <div id="page-container" ref={pageContainerRef}>
            <ErrorBoundary>
                <Switch>
                    {/* -------------------------- */}
                    {publicRoutesMap}
                    {privateRoutesMap}
                    {/* --------------------------- */}
                    <PrivateRoute
                        exact
                        path="/404NotFound"
                        title="Not Found"
                        component={NotFoundPage}
                    />
                    {/* _------------------- */}

                    {localStorage.getItem("token") ? (
                        <PrivateRoute path="*" component={NotFoundPage} />
                    ) : (
                        <Redirect to={{ pathname: "/home", state: { from: currentLocation } }} />
                    )}
                </Switch>
            </ErrorBoundary>
        </div>
    );
}

export default Routes;
