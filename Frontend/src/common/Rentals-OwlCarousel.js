// Import necessary packages
import React from 'react';
import OwlCarousel from 'react-owl-carousel';
// import 'owl.carousel/dist/assets/owl.carousel.css';
// import 'owl.carousel/dist/assets/owl.theme.default.css';

import {
  Card, Button, CardTitle, CardText, CardBody, CardFooter
} from 'reactstrap';


import Rentals1 from '../asset/images/Rentals-1.png';
import Rentals2 from '../asset/images/Rentals-2.png';
import Rentals3 from '../asset/images/Rentals-3.png';
import Rentals4 from '../asset/images/Rentals-4.png';
import Rentals5 from '../asset/images/Rentals-5.png';

// import leftarrow from '../images/left-arrow.png';
// import rightarrow from '../asset/images/right-arrow.png';



// Define the functional component
const Rentals_OwlCarousel = () => {
  // Define the options for the carousel
  const options = {
    loop: true,
    nav: true,
    margin: 0,
    autoplay: false,
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

    <OwlCarousel {...options} className='owl-theme' id='Rentals' nav>
      <div class='item'>
        <a href=''>
          <img src={Rentals1} alt='Rentals1' />
          <span>Camp</span>
          </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Rentals2} alt='Rentals2' />
          <span>Decor</span>
          </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Rentals3} alt='Rentals3' />
          <span>Chair</span>
          </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Rentals4} alt='Rentals4' />
          <span>Tools</span>
          </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Rentals5} alt='Rentals5' />
          <span>Play area</span>
          </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Rentals1} alt='Rentals1' />
          <span>Camp</span>
        </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Rentals2} alt='Rentals2' />
          <span>Decor</span>
        </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Rentals3} alt='Rentals3' />
          <span>Chair</span>
        </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Rentals4} alt='Rentals4' />
          <span>Tools</span>
        </a>
      </div>
      <div class='item'>
        <a href=''>
          <img src={Rentals5} alt='Rentals5' />
          <span>Play area</span>
        </a>
      </div>
    </OwlCarousel>

  );
};

export default Rentals_OwlCarousel;
