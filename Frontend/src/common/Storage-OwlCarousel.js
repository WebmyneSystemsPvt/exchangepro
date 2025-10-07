// Import necessary packages
import React from 'react';
import OwlCarousel from 'react-owl-carousel';
// import 'owl.carousel/dist/assets/owl.carousel.css';
// import 'owl.carousel/dist/assets/owl.theme.default.css';

import {
  Card, Button, CardTitle, CardText, CardBody, CardFooter
} from 'reactstrap';


import Storage1 from '../asset/images/Storage-1.png';
import Storage2 from '../asset/images/Storage-2.png';
import Storage3 from '../asset/images/Storage-3.png';
import Storage4 from '../asset/images/Storage-4.png';
import Storage5 from '../asset/images/Storage-5.png';



// Define the functional component
const Storage_OwlCarousel = () => {
  // Define the options for the carousel
  const options = {
    loop: true,
    nav: true,
    autoplay: false,
    margin: 0,
    navText: ["<i class='fal fa-chevron-left'></i>", "<i class='fal fa-chevron-right'></i>"],
    dots: false,

    responsive: {
      0: {
        items: 3
      },
      768: {
        items: 4
      },
      1200: {
        items: 5,
      }
    }
  };

  return (

    <OwlCarousel {...options} className='owl-theme'  id='Storage' nav>
      <div class='item'>
       <a href=''> 
        <img src={Storage1} alt='Storage1' />
          <span>Boat</span>
       </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Storage2} alt='Storage2' />
          <span>Truck</span>
          </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Storage3} alt='Storage3' />
          <span>Store</span>
          </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Storage4} alt='Storage4' />
          <span>Car</span>
          </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Storage5} alt='Storage5' />
          <span>Garag</span>
        </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Storage1} alt='Storage1' />
          <span>Boat</span>
        </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Storage2} alt='Storage2' />
          <span>Truck</span>
        </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Storage3} alt='Storage3' />
          <span>Store</span>
        </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Storage4} alt='Storage4' />
          <span>Car</span>
        </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Storage5} alt='Storage5' />
          <span>Garag</span>
        </a>
      </div>
    </OwlCarousel>

  );
};

export default Storage_OwlCarousel;
