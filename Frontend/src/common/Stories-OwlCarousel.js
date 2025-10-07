// Import necessary packages
import React from 'react';
import OwlCarousel from 'react-owl-carousel';
// import 'owl.carousel/dist/assets/owl.carousel.css';
// import 'owl.carousel/dist/assets/owl.theme.default.css';

import client1 from '../asset/images/client-1.png';
import client2 from '../asset/images/client-2.png';
import client3 from '../asset/images/client-3.png';



// Define the functional component
const StoriesOwldemo = () => {
  // Define the options for the carousel
  const options = {
    loop: true,
    autoplay:true,
    nav: true,
    navText: ["<i class='fal fa-chevron-left'></i>", "<i class='fal fa-chevron-right'></i>"],
    dots: false,
    center: true,
    responsive: {
      0: {
        items: 1
      },
      500: {
        items: 1
      },
      768: {
        items: 1
      },
      1000: {
        items: 3,
        margin: 20,
      }
    }
  };

  return (
    
      <OwlCarousel {...options} className='owl-theme' id='stories' nav>
        <div class='item'>
            <div className='stories_item'>
          <img src={client1} top width="100%" alt="Card cap" />
          <p>Aliquam rutrum bibendum sem non posuere. Etiam bibendum consectetur enim a maximus. Vestibulum egestas nulla quis lectus fermentum consectetur. Sed nec faucibus felis, vel volutpat turpis. Aenean tincidunt dapibus dui, ut laoreet elit.</p>
          <span>Patricia Walker</span>
            </div>
        </div>
        <div class='item'>
            <div className='stories_item'>
          <img src={client2} top width="100%" alt="Card cap" />
          <p>Aliquam rutrum bibendum sem non posuere. Etiam bibendum consectetur enim a maximus. Vestibulum egestas nulla quis lectus fermentum consectetur. Sed nec faucibus felis, vel volutpat turpis. Aenean tincidunt dapibus dui, ut laoreet elit.</p>
          <span>Patricia Walker</span>
            </div>
        </div>
        <div class='item'>
            <div className='stories_item'>
          <img src={client3} top width="100%" alt="Card cap" />
          <p>Aliquam rutrum bibendum sem non posuere. Etiam bibendum consectetur enim a maximus. Vestibulum egestas nulla quis lectus fermentum consectetur. Sed nec faucibus felis, vel volutpat turpis. Aenean tincidunt dapibus dui, ut laoreet elit.</p>
          <span>Patricia Walker</span>
            </div>
        </div>
        <div class='item'>
            <div className='stories_item'>
          <img src={client1} top width="100%" alt="Card cap" />
          <p>Aliquam rutrum bibendum sem non posuere. Etiam bibendum consectetur enim a maximus. Vestibulum egestas nulla quis lectus fermentum consectetur. Sed nec faucibus felis, vel volutpat turpis. Aenean tincidunt dapibus dui, ut laoreet elit.</p>
          <span>Patricia Walker</span>
            </div>
        </div>
        <div class='item'>
            <div className='stories_item'>
          <img src={client2} top width="100%" alt="Card cap" />
          <p>Aliquam rutrum bibendum sem non posuere. Etiam bibendum consectetur enim a maximus. Vestibulum egestas nulla quis lectus fermentum consectetur. Sed nec faucibus felis, vel volutpat turpis. Aenean tincidunt dapibus dui, ut laoreet elit.</p>
          <span>Patricia Walker</span>
            </div>
        </div>
        <div class='item'>
            <div className='stories_item'>
          <img src={client3} top width="100%" alt="Card cap" />
          <p>Aliquam rutrum bibendum sem non posuere. Etiam bibendum consectetur enim a maximus. Vestibulum egestas nulla quis lectus fermentum consectetur. Sed nec faucibus felis, vel volutpat turpis. Aenean tincidunt dapibus dui, ut laoreet elit.</p>
          <span>Patricia Walker</span>
            </div>
        </div>

      </OwlCarousel>
    
  );
};

export default StoriesOwldemo;
