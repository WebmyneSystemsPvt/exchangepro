import { useEffect } from 'react';
import { useLocation } from 'react-router-dom';

const useScrollToTop = () => {
    const location = useLocation();

    useEffect(() => {
        // console.log('Pathname changed:', location.pathname);
        window.scrollTo(0, 0);
    }, [location?.pathname]);
};

export default useScrollToTop;
