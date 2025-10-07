import { yupResolver } from '@hookform/resolvers/yup';
import React from 'react';
import { useHistory, useLocation } from 'react-router-dom';
import location1 from '../../asset/images/location_icon1.png';

import customer_1 from '../../asset/images/customer_img_1.png';

import { Box, Grid, Rating, TextField } from '@mui/material';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import OwlCarousel from 'react-owl-carousel';
import { useDispatch, useSelector } from 'react-redux';
import { useParams } from 'react-router-dom';
import message_1 from '../../asset/images/message_1.png';
import { addRec, postFetch } from '../../common/common';
import GoogleMapModal from '../../common/GoogleMapModal';
import { PageLoader } from '../../common/Loader';
import { OpenLogin, SearchProperties } from '../../store/actions/common';
import { AddReviewValidationSchema } from '../../utils/formValidationSchemas';
import { ErrorToast, SuccessToast } from '../../utils/Toaster';
import ListSearch from '../SearchBar/ListSearch';
import HoverRating from './RatingComp';

const options = {
    loop: true,
    nav: true,
    margin: 0,
    items: 1,
    autoplay: true,
    navText: ["<i class='fal fa-chevron-left'></i>", "<i class='fal fa-chevron-right'></i>"],
    dots: true,
}


const ItemDetail = () => {

    const history = useHistory();
    const location = useLocation();
    const dispatch = useDispatch();
    let { id } = useParams();
    const { Coordinates } = useSelector((state) => state.common);
    const { token } = useSelector((state) => state.user);


    const {
        register,
        handleSubmit,
        setValue,
        reset,
        formState: { errors }
    } = useForm({
        defaultValues: {
            rating: 0,
            title: "",
            description: "",
        },
        resolver: yupResolver(AddReviewValidationSchema),
    });


    const [itemData, setItemData] = useState(null)
    const [Loader, setLoader] = useState(false);
    const [AddReview, setAddReview] = useState(false);

    const getItemStorage = async (id) => {
        setLoader(true)
        let req = { "id": id, ...Coordinates }
        let ItemsResult = await postFetch(`/get-item-storage`, req);
        if (ItemsResult?.status) {
            if (ItemsResult?.responseData?.data[0]) {
                setItemData(ItemsResult?.responseData?.data[0])
            } else {
                ErrorToast(ItemsResult?.message)
                history.push("/list-item")
            }
        }
        setLoader(false)
    };

    useEffect(() => {
        if (id) {
            getItemStorage(id)
            dispatch(SearchProperties(true))
        }
    }, [id])

    useEffect(() => {
        if (token) {
            setAddReview(false)
        }
    }, [token])


    const getItemStorageASD = async (item) => {
        history.push({
            pathname: "/list-item",
            state: { item: false, itemReq: item },
            // state: { item: false, itemReq: { ...item, dateRange: [] } },
        });
    }

    const ViewReviewPart = async () => {
        if (token) {
            setAddReview(true)
        } else {
            dispatch(OpenLogin({ reg: false, modal: true }))
        }
    }

    const [loading, setLoading] = useState(false);

    const onSubmit = async (data) => {
        setLoading(true)
        console.log("🚀 ~ onSubmit ~ data:", data)
        data = { ...data, "item_storage_id": id }
        let result = await addRec(`/give-rating`, data);
        if (result?.status) {
            SuccessToast(result?.message)
            reset()
            setAddReview(false)
            getItemStorage(id)
        } else {
            ErrorToast(result?.message)
        }
        setLoading(false)
    }

    return (Loader
        ? <PageLoader /> :
        <div className="LIST_PAGE">
            <ListSearch
                SearchReq={{}}
                SubmitSearch={(req) => {
                    getItemStorageASD(req);
                    // setValue("SearchReq", req)
                }} />
            <div classNameName='item_detail_page'>
                <section className="inner_part comnpadding">
                    <div className='container'>
                        <div className='row'>
                            <div className='col-lg-6'>
                                <OwlCarousel {...options} className='owl-theme' id='Product_Owl' nav>
                                    {itemData && itemData?.photos?.length > 0 ? itemData?.photos.map((obj) =>
                                        <div class='item'>
                                            <img src={obj?.item_photo} alt='item_slider' />
                                        </div>
                                    ) : (
                                        <div class='item'>
                                            <img src={itemData?.default_storage_photo} alt='item_slider' />
                                        </div>
                                    )}
                                </OwlCarousel>
                            </div>
                            <div className='col-lg-6'>
                                <div className='controlled'>
                                    <h2 className='comntitle2'>{itemData?.listing_type}</h2>
                                    <div className='rating'>
                                        <span>
                                            <i className='fas fa-star'></i>
                                            <i className='fas fa-star'></i>
                                            <i className='fas fa-star'></i>
                                            <i className='fas fa-star'></i>
                                            <i className='fas fa-star'></i>
                                            <small> 5.0</small>
                                        </span>
                                        <div className='report'>
                                            <span><i className='fas fa-flag'></i> Report this listing </span>
                                            <span><i className='far fa-heart'></i> Save </span>
                                        </div>
                                    </div>
                                    <div className='pricice_div'>
                                        <span className='per_day'>{itemData?.amount_in_text}</span>
                                        <div className="profile_content">
                                            <img src={itemData?.seller?.avatar} alt='profile' />
                                            <div>
                                                <small>Owner: <span>{itemData?.seller?.name}</span>
                                                    <img src={message_1} alt='message' />
                                                </small>
                                                <small>
                                                    {itemData?.seller?.seller_details?.location}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <p>
                                        {itemData?.exception_details.slice(0, 200) + "..."}
                                    </p>
                                    <div className="from_to">
                                        <input type="date" />
                                        <label>To</label>
                                        <input type="date" />
                                    </div>
                                    <div className="you_pay">
                                        <button className="Red_Btn">Borrow</button> <span>You Pay: {itemData?.currency}{(itemData?.application_fee + itemData?.application_fee + itemData?.others_fee + itemData?.tax).toFixed(2)}</span>
                                    </div>
                                    <ul className="other_details">
                                        <li>
                                            <span>{itemData?.currency}{itemData?.application_fee} x 1 days</span>
                                            <span>{itemData?.currency}{itemData?.application_fee}</span>
                                        </li>
                                        <li>
                                            <span>Application fees</span>
                                            <span>{itemData?.currency}{itemData?.application_fee}</span>
                                        </li>
                                        <li>
                                            <span>Other fees</span>
                                            <span>{itemData?.currency}{itemData?.others_fee}</span>
                                        </li>
                                        <li>
                                            <span>Taxes</span>
                                            <span>{itemData?.currency}{itemData?.tax}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="product_details">
                    <div className='container'>
                        <div className='row'>
                            <div className="col-lg-9">
                                <div className="offers">
                                    <p>
                                        {itemData?.exception_details}
                                    </p>
                                    <h4>
                                        Additionally, our facility offers:
                                    </h4>
                                    <ul className="secure_entry">
                                        {itemData && itemData?.offers?.length > 0 && itemData?.offers.map((obj) =>
                                            <li>
                                                <div className="condition_icon">
                                                    <img src={obj?.photo} alt='AD' />
                                                </div>
                                                <div className="condition">
                                                    <h6>
                                                        {obj?.title}
                                                    </h6>
                                                    <p>
                                                        {obj?.description}
                                                    </p>
                                                </div>
                                            </li>
                                        )}
                                    </ul>
                                </div>
                            </div>
                            <div className="col-lg-3">
                                <div className="main_profile">
                                    <div className="name_img">
                                        <img src={itemData?.seller?.avatar} alt='profile' />
                                        <span>{itemData?.seller?.name}</span>
                                    </div>
                                    <h4>Add in groups</h4>
                                    <ul>
                                        <li>
                                            <img src={location1} alt='location' />
                                            <small>{itemData?.seller?.seller_details?.location}</small>
                                            {/* <small>{itemData?.seller?.seller_details?.location} <span className='font_red'>+4</span></small> */}
                                        </li>
                                    </ul>
                                    <h4>Availability</h4>
                                    <div className="datediv">
                                        <span>Mon - Fri
                                            <small>{itemData?.seller?.seller_details?.availabilityMF}</small>
                                        </span>
                                        <span>Sat - Sun
                                            <small>{itemData?.seller?.seller_details?.availabilitySS}</small>
                                        </span>
                                    </div>
                                    <p>
                                        Have questions about this rental?
                                    </p>
                                    <button className="btn_button">Contact now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {itemData && itemData?.items?.length > 0 &&
                    <section className='Available_Storage'>
                        <div className="container">
                            <div className='row'>
                                <div className="col-lg-12">
                                    <h2>Featuring available storage:</h2>
                                    <ul className='list_storage'>
                                        {itemData?.items.map((obj) =>
                                            <li>{obj?.item_name}</li>
                                        )}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>
                }
                {!AddReview ?
                    <>
                        <section className="feedback comnpadding">
                            <div className='container'>
                                <div className='row'>
                                    <div className="col-12">
                                        <div className="top_saying">
                                            <div className="people_view">
                                                <div className="star_rating">
                                                    <strong>2.5</strong>
                                                    {/* <strong>{itemData?.rating_avg}</strong> */}
                                                    {/* <span>
                                                <i className='fas fa-star'></i>
                                                <i className='fas fa-star'></i>
                                                <i className='fas fa-star'></i>
                                                <i className='fas fa-star'></i>
                                                <i className='fas fa-star'></i>
                                            </span> */}
                                                    <Box component="fieldset" mb={3}  >
                                                        <Rating name="half-rating-read" defaultValue={2.5} precision={0.5} readOnly />
                                                    </Box>
                                                </div>
                                                <div>
                                                    <p>
                                                        100% of people given 5 to 5 star
                                                    </p>
                                                    {/* <span className='black_light'>(999+ reviews)</span> */}
                                                </div>
                                            </div>
                                            <a href="javascript:void(0);" onClick={() => ViewReviewPart()} className="btn_button font_red">Write about your experience</a>
                                        </div>
                                        <ul className="customer_review">
                                            {itemData && itemData?.review?.length > 0 && itemData?.review.map((obj) =>
                                                <li>
                                                    <img src={customer_1} alt='customer' />
                                                    <div className="customer_name">
                                                        <h6>{obj?.reviewer_name}<span> - {obj?.day_ago}</span></h6>
                                                        <p>{obj?.description}</p>
                                                    </div>
                                                </li>
                                            )}
                                        </ul>
                                        {itemData && itemData?.review?.length > 0 && <a href="#" className="Outline_Btn">Read all {itemData?.review?.length} reviews</a>}
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section className="map">
                            <div className='container'>
                                <div className='row'>
                                    <div className="col-12">
                                        <div className="location_map">
                                            <GoogleMapModal coordinates={{
                                                lat: parseFloat(itemData?.latitude),
                                                lng: parseFloat(itemData?.longitude)
                                            }} />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <section className="privacey_box comnpadding">
                            <div className='container'>
                                <div className='row'>
                                    <div className="col-12">
                                        <h4>Terms and Conditions:</h4>
                                        <ul className="terms_provision">
                                            {itemData && itemData?.termsConditions?.length > 0 && itemData?.termsConditions.map((obj) =>
                                                <li>
                                                    {obj?.description}
                                                </li>
                                            )}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </> : <>
                        <section className="feedback comnpadding">
                            <div className='container'>
                                <div className='ft'>
                                    <div className="col-12">
                                        <a href="javascript:void(0);" onClick={() => ViewReviewPart()} className="btn_button font_red">Write about your experience </a>
                                        <HoverRating UserRating={(data) => setValue("rating", data)} />
                                        <Grid className="mb-3">
                                            <TextField
                                                error={!!errors?.title?.message}
                                                helperText={errors?.title?.message}
                                                {...register('title')}
                                                fullWidth
                                                label="Title Of Review"
                                            />
                                        </Grid>
                                        <Grid className="mb-3">
                                            <TextField
                                                error={!!errors?.description?.message}
                                                helperText={errors?.description?.message}
                                                {...register('description')}
                                                fullWidth
                                                label="Detailed description of the experience "
                                                multiline
                                                rows={2}
                                            />
                                        </Grid>
                                        <button
                                            class="Red_Btn"
                                            disabled={loading}
                                            onClick={handleSubmit(onSubmit)}
                                        >
                                            {loading ? <i className="fa fa-spinner fa-spin"></i> : "Share Experience"}
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </section>
                    </>
                }



            </div>
        </div>
    );
};

export default ItemDetail;
