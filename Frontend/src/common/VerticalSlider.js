import React, { useEffect, useRef, useState } from 'react';
import Slider from 'react-slick';
// import Img_1 from '../asset/images/banner-pic/1.png';
import { fetchListing } from './common';

const VerticalSlider = () => {
    const settings = {
        dots: false,
        vertical: true,
        arrows: false,
        autoplay: false,
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1,
        verticalSwiping: false,
        swipeToSlide: false,
        draggable: false,
        swipe: false,
    };

    const [sliders, setSliders] = useState([]);
    const sliderRefs = useRef([]);

    useEffect(() => {
        const interval = setInterval(() => {
            if (sliders.length > 0) {
                const randomIndex = Math.floor(Math.random() * sliders.length);
                if (sliderRefs.current[randomIndex]) {
                    sliderRefs.current[randomIndex].slickNext();
                }
            }
        }, 2500);
        return () => clearInterval(interval);
    }, [sliders]);

    useEffect(() => {
        BannersList();
    }, []);

    const BannersList = async () => {
        let BannersResult = await fetchListing(`/banners`);
        if (BannersResult?.status) {
            setSliders(BannersResult?.responseData);
        }
    }

    return (
        <ul className="main_slider">
            {sliders.length > 0 && sliders.map((slider, index) => (
                <li key={index}>
                    <div className={slider.className}>
                        <Slider ref={(el) => (sliderRefs.current[index] = el)} {...settings}>
                            {slider.map((src, imgIndex) => (
                                <div key={imgIndex}>
                                    <img src={src.photo} alt="" />
                                </div>
                            ))}
                        </Slider>
                    </div>
                </li>
            ))}
        </ul>
    );
};

export default VerticalSlider;
