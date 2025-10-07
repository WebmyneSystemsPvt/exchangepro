import React, { useEffect, useState } from "react";
import StoriesOwldemo from './../../common/Stories-OwlCarousel';

import SearchBar from '../SearchBar/SearchBar';

import work_icon1 from '../../asset/images/icon-1.png';
import work_icon2 from '../../asset/images/icon-2.png';
import work_icon3 from '../../asset/images/icon-3.png';
import work_icon4 from '../../asset/images/icon-4.png';


import Logo from '../../asset/images/logo.png';

import { Container, Popover } from "@mui/material";
import filter_btn from '../../asset/images/filter-btn.png';
import PriceRangeFilter from './../SearchBar/PriceRangeFilter';

import { useSelector } from "react-redux";
import { useHistory } from 'react-router-dom';
import Slider from 'react-slick';
import CardComp from "../../common/Card";
import { fetchListing, postFetch } from "../../common/common";
import { PageLoader } from "../../common/Loader";
import VerticalSlider from "../../common/VerticalSlider";


let OwlOption = {
    loop: true,
    nav: true,
    autoplay: true,
    navText: ["<i class='fal fa-chevron-left'></i>", "<i class='fal fa-chevron-right'></i>"],
    dots: false,

    responsive: {
        0: {
            items: 1
        },
        768: {
            items: 2
        },
        1000: {
            items: 4,
            margin: 0,
        },
        1700: {
            items: 4,
            margin: 10,
        }
    }
}

const Category_Sub_C_Options = {
    loop: true,
    nav: true,
    margin: 0,
    autoplay: true,
    navText: ["<i class='fal fa-chevron-left'></i>", "<i class='fal fa-chevron-right'></i>"],
    dots: false,

    responsive: {
        0: {
            items: 5
        },
        768: {
            items: 5
        },
        1200: {
            items: 5,
        }
    }
};

const CatSettings = {
    dots: false,
    arrows: false,
    infinite: true,
    speed: 500,
    slidesToShow: 5,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 1500,
    responsive: [
        {
            breakpoint: 1024,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: true,
                dots: true
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
                initialSlide: 2
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
};

const POPSettings = {
    dots: false,
    arrows: false,
    infinite: true,
    speed: 500,
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 2000,
    responsive: [
        {
            breakpoint: 1024,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: true,
                dots: true
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
                initialSlide: 2
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
};




const Home = () => {

    const history = useHistory();

    const { Coordinates } = useSelector((state) => state.common);

    const [SearchData, setSearchData] = useState(null)

    const [anchorEl2, setAnchorEl2] = useState(null);
    const [PriceModal, setPriceModal] = useState(null);
    const [Loader, setLoader] = useState(false);
    const [CategoriesList, setCategoriesList] = useState([]);
    const [PriceRange, setPriceRange] = useState({ price_min: 0, price_max: 0 });

    const PriceId = PriceModal ? "simple-popover" : undefined;

    const handlePriceClick = (event) => {
        setAnchorEl2(event.currentTarget);
        setPriceModal(true);
    };

    const ItemStorageDetails = async (data) => {
        // setSearchData(data)
        const { price_max, price_min } = PriceRange
        let req = { ...data, "price_min": price_min, "price_max": price_max }
        RedirectPage(false, req)
    }


    useEffect(() => {
        getItemStorage(Coordinates);
    }, [Coordinates]);
    useEffect(() => {
        Get_Category_SubCategory();
    }, []);

    const getItemStorage = async (req) => {
        setLoader(true)
        let ItemsResult = await postFetch(`/get-item-storage`, req);
        if (ItemsResult?.status) {
            setSearchData(ItemsResult?.responseData?.data);
        }
        setLoader(false)
    };

    const Get_Category_SubCategory = async () => {
        let resultCategory = await fetchListing(`/categories`);
        if (resultCategory?.status) {
            setCategoriesList(resultCategory?.responseData);
        }
    };

    const RedirectPage = async (item, req, viewDetails) => {
        if (!item) {
            history.push({
                pathname: "/list-item",
                state: { item: false, itemReq: req, from: "MainSearch" },
            });
        } else {
            if (viewDetails) {
                history.push({
                    pathname: `/item-details/${item?.id}`,
                    state: item,
                });
            } else {
                history.push({
                    pathname: "/list-item",
                    state: { item: false, itemReq: req, from: "FromCategory" },
                });
            }
        }
    };

    const PriceRangeFunc = (value) => {
        setPriceRange({ price_min: value[0], price_max: value[1] })
    };

    return (
        <>

            <section className="banner">
                {/* <img src={HomeBanner} alt="Home Banner" /> */}
                <VerticalSlider />
                <div className='banner_serch'>
                    <div className="logo">
                        <span><img src={Logo} alt="Logo" /></span>
                    </div>
                    <Container className="top_search">
                        <SearchBar SearchReq={ItemStorageDetails} />
                    </Container>
                    <div className="banner_category">
                        <div className="banner_category_slider rent">
                            {CategoriesList.length > 0 && CategoriesList[0] && (
                                <>
                                    <span>{CategoriesList[0]?.name}</span>
                                    <Slider {...CatSettings}>
                                        {CategoriesList[0]?.sub_categories?.length > 0 && CategoriesList[0]?.sub_categories?.map((S_Item) => {
                                            return (
                                                <div class='item' id={S_Item.id}>
                                                    <a onClick={() => RedirectPage(true, S_Item, false)}>
                                                        <img src={S_Item?.photo} alt='Rentals1' />
                                                        <span >{S_Item?.name}</span>
                                                    </a>
                                                </div>
                                            )
                                        })}
                                    </Slider>
                                </>
                            )}
                        </div>


                        {/* <OwlCarousel {...Category_Sub_C_Options} className='owl-theme' id='Rentals' nav>
                                        {CategoriesList[0]?.sub_categories?.length > 0 && CategoriesList[0]?.sub_categories?.map((S_Item) => {
                                            return (
                                                <div className='item' id={S_Item.id} key={S_Item.id}>
                                                    <div
                                                        role='button'
                                                        onClick={() => RedirectPage(true, S_Item, false)}
                                                        style={{ cursor: 'pointer' }}
                                                    >
                                                        <img src={S_Item?.photo} alt='Rentals1' />
                                                        <span>{S_Item?.name}</span>
                                                    </div>
                                                </div>
                                            )
                                        })}
                                    </OwlCarousel> */}
                        <div className="banner_category_slider rent">
                            {CategoriesList.length > 0 && CategoriesList[1] && (
                                <>
                                    <span>{CategoriesList[1]?.name}</span>
                                    <Slider {...CatSettings}>
                                        {CategoriesList[1]?.sub_categories?.length > 0 && CategoriesList[1]?.sub_categories?.map((S_Item) => {
                                            return (
                                                <div class='item' id={S_Item.id}>
                                                    <a onClick={() => RedirectPage(true, S_Item, false)}>
                                                        <img src={S_Item?.photo} alt='Rentals1' />
                                                        <span >{S_Item?.name}</span>
                                                    </a>
                                                </div>
                                            )
                                        })}
                                    </Slider>
                                </>
                            )}
                        </div>
                        <div className="filterbtn">
                            <span>Filters</span>
                            <button><img src={filter_btn} onClick={handlePriceClick} alt="Filter" /></button>
                        </div>
                    </div>
                </div>
            </section>

            <section className="comnpadding popular_items">
                <div className="container">
                    <div className="row">
                        <div className="col-md-12">
                            <h1 className="comntitle">POPULAR <span>LISTING</span></h1>
                            {Loader
                                ? <PageLoader />
                                : <Slider {...POPSettings} >
                                    {SearchData && SearchData.map((item) =>
                                        <div class='item'>
                                            <CardComp item={item} RedirectPage={(item) => RedirectPage(item, null, true)} />
                                        </div>
                                    )}
                                </Slider>}
                        </div>
                    </div>
                </div>
            </section>

            <section className="how_works">
                <div className="container">
                    <div className="row">
                        <div className="col-md-12">
                            <h2 className="comntitle">HOW IT <span>WORKS</span></h2>
                            <ul>
                                <li>
                                    <div className="work_icon">
                                        <img src={work_icon1} alt="How it works" />
                                    </div>
                                    <div className="work_content">
                                        <span>1</span>
                                        <h3>Browse Listings</h3>
                                        <p>Search and filter through various items and storage spaces to find what you need.</p>
                                    </div>
                                </li>
                                <li>
                                    <div className="work_icon">
                                        <img src={work_icon2} alt="How it works" />
                                    </div>
                                    <div className="work_content">
                                        <span>2</span>
                                        <h3>Book Your Selection</h3>
                                        <p>Select the item or space you want, choose your dates, and make a reservation.</p>
                                    </div>
                                </li>
                                <li>
                                    <div className="work_icon">
                                        <img src={work_icon3} alt="How it works" />
                                    </div>
                                    <div className="work_content">
                                        <span>3</span>
                                        <h3>Confirm and Pay</h3>
                                        <p>Review your reservation details and complete the payment process.</p>
                                    </div>
                                </li>
                                <li>
                                    <div className="work_icon">
                                        <img src={work_icon4} alt="How it works" />
                                    </div>
                                    <div className="work_content">
                                        <span>4</span>
                                        <h3>Use or Access</h3>
                                        <p>Pick up your item or start using your rented storage space as per your reservation.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section className="comnpadding success_stories">
                <div className="container">
                    <div className="row">
                        <div className="col-md-12">
                            <h2 className="comntitle">SUCCESS <span>STORIES</span></h2>
                            <StoriesOwldemo />
                        </div>
                    </div>
                </div>
            </section>

            <Popover
                id={PriceId}
                open={PriceModal}
                anchorEl={anchorEl2}
                onClose={() => setPriceModal(false)}
                anchorOrigin={{
                    vertical: "bottom",
                    horizontal: "left",
                }}
                transformOrigin={{
                    vertical: "top",
                    horizontal: "left",
                }}
            >
                <PriceRangeFilter PriceRange={PriceRangeFunc} Data={PriceRange} />
            </Popover>


        </>
    )
}

export default Home