// Import necessary packages
import React from 'react';
import OwlCarousel from 'react-owl-carousel';
// import 'owl.carousel/dist/assets/owl.carousel.css';
// import 'owl.carousel/dist/assets/owl.theme.default.css';

import {
  Card, Button, CardTitle, CardText, CardBody, CardFooter
} from 'reactstrap';


import item_slider from '../asset/images/inner_slide_1.png';

// import leftarrow from '../images/left-arrow.png';
// import rightarrow from '../asset/images/right-arrow.png';



// Define the functional component
const Product_OwlCarousel = () => {
  // Define the options for the carousel
  const options = {
    loop: true,
    nav: true,
    margin: 0,
    items: 1,
    // autoplay: true,
    navText: ["<i class='fal fa-chevron-left'></i>", "<i class='fal fa-chevron-right'></i>"],
    // dots: false,
    dots: true,

    // responsive: {
    //   0: {
    //     items: 3
    //   },
    //   768: {
    //     items: 4
    //   },
    //   1200: {
    //     items: 5,
    //   }
    // }
  };

  return (

    <OwlCarousel {...options} className='owl-theme' id='Product_Owl' nav>
      <div class='item'>
          <img src={item_slider} alt='item_slider' />
      </div>
      <div class='item'>
        <img src={item_slider} alt='item_slider' />
      </div>
      <div class='item'>
        <img src={item_slider} alt='item_slider' />
      </div>
      <div class='item'>
        <img src={item_slider} alt='item_slider' />
      </div>
      <div class='item'>
        <img src={item_slider} alt='item_slider' />
      </div>
    </OwlCarousel>

  );
};

export default Product_OwlCarousel;
