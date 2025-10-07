import React from 'react';
import OwlCarousel from 'react-owl-carousel';
import 'owl.carousel/dist/assets/owl.carousel.css';
import 'owl.carousel/dist/assets/owl.theme.default.css';
import img_1 from "../asset/images/item-1.png"

const VerticalScrollAnimation = () => {
    const generateCellContent = () => {
        const cells = [];
        for (let i = 0; i < 25; i++) { // 5x5 grid, total 25 cells
            cells.push(
                <div className="cell" key={i}>
                    <img src={img_1} alt={`Item ${i + 1}`} />
                    {/* You can replace the img tag with any content you want */}
                </div>
            );
        }
        return cells;
    };

    const owlOptions = {
        items: 1,
        loop: true,
        margin: 10,
        nav: false,
        dots: false,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    };

    return (
        <div className="vertical-carousel-container">
            <OwlCarousel className="owl-theme" {...owlOptions}>
                <div className="cube-grid">
                    {generateCellContent()}
                </div>
            </OwlCarousel>
        </div>
    );
};

export default VerticalScrollAnimation;
