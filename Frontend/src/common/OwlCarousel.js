import React from 'react';
import OwlCarousel from 'react-owl-carousel';
import { useHistory } from 'react-router-dom';
import CardComp from './Card';
import { PageLoader } from './Loader';

const Owldemo = ({ DataList = [], OwlOption = {}, Loader, RedirectPage }) => {

  return (Loader
    ? <PageLoader />
    : <OwlCarousel {...OwlOption} className='owl-theme' id='popular' nav>
      {DataList && DataList.map((item) =>
        <div class='item'>
          <CardComp item={item} RedirectPage={RedirectPage} />
        </div>
      )}
    </OwlCarousel>
  );
};

export default Owldemo;