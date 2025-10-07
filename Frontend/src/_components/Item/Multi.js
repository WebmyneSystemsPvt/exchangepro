import { yupResolver } from '@hookform/resolvers/yup';
import { Autocomplete, Button, Checkbox, Container, FormHelperText, Grid, InputLabel, ListItemText, MenuItem, Select, TextField, Typography } from '@mui/material';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import DatePicker from 'react-datepicker';
import { useForm, Controller, FormProvider, useFormContext, useFieldArray, useWatch, } from 'react-hook-form';
import { useHistory } from 'react-router-dom';
import { TagsInput } from "react-tag-input-component";
import { addRec, fetchListing } from '../../common/common';
import FileUploadComponent from '../../utils/FileUploadComponent';
import { AddItemValidation, step1ValidationSchema, step2ValidationSchema, step3ValidationSchema } from '../../utils/formValidationSchemas';
import { ErrorToast, SuccessToast } from '../../utils/Toaster';
import { ChangeKeyValue, convertToFormData } from '../../utils/utils';
import { useSelector } from 'react-redux';


const AddItem = () => {


    const [step, setStep] = useState(0);


    const StepsLabel = [
        "Step",
        "Step",
        "Step",
        "Step",
    ];

    const {
        register,
        handleSubmit,
        control,
        getValues,
        setValue,
        watch,
        formState: { errors,isDirty }
    } = useForm({
        defaultValues: {
            listing_type: "",
            categories_id: null,
            sub_categories_id: null,
            location: "",


            description: "",
            country: "",
            state: "",
            city: "",
            pincode: "",
            landmark: "",
            latitude: "",
            longitude: "",
            tags: [],

            newTag: '',

            exception_details: "",
            rate: "",
            rented_max_allow_days: 0,
            blocked_days: [],
            item_id: [],
            default_storage_photo: [],
            storage_photos: [],

            terms_conditions: [],
            facility_offers: []
        },
        // resolver: yupResolver(AddItemValidation),
        resolver: yupResolver([step1ValidationSchema, step2ValidationSchema, step3ValidationSchema][step]),
    });

    const categories_id_val = useWatch({
        control,
        name: "categories_id",
    });
    const location_val = useWatch({
        control,
        name: "location",
    });
    const pincode_val = useWatch({
        control,
        name: "pincode",
    });
    const latitude_val = useWatch({
        control,
        name: "latitude",
    });
    const longitude_val = useWatch({
        control,
        name: "longitude",
    });


    const history = useHistory();

    const { token, info } = useSelector((state) => state.user);

    useEffect(() => {
        if (token && info?.role_id !== 3) {
            return history.push("/account")
        }
    }, [info, token]);

    const [CategoryData, setCategoryData] = useState([])
    const [subCategories, setSubCategories] = useState([])
    const [ItemList, setItemList] = useState([])
    const [loading, setLoading] = useState(false)


    useEffect(() => {
        GetAllList()
    }, [])

    const GetAllList = async () => {
        let categoriesResult = await fetchListing(`/categories`);
        if (categoriesResult?.status) {
            setCategoryData(ChangeKeyValue(categoriesResult?.responseData, "name"))
        }
        let subCategoriesResult = await fetchListing(`/sub-categories`);
        if (subCategoriesResult?.status) {
            setSubCategories(ChangeKeyValue(subCategoriesResult?.responseData, "name"))
        }
        let ItemsResult = await fetchListing(`/items`);
        if (ItemsResult?.status) {
            setItemList(ChangeKeyValue(ItemsResult?.responseData, "item_name"))
        }
    }

    const onSubmit = async (data, event) => {
        event.preventDefault();
        console.log(data);
        setLoading(true)

        let terms_conditionsArr = data.terms_conditions
        let facility_offersArr = data.facility_offers

        delete data.terms_conditions
        delete data.facility_offers

        data.blocked_days = data.blocked_days.join(', ');
        if (data.default_storage_photo) {
            data.default_storage_photo = data.default_storage_photo[0];
        }

        let formData = convertToFormData(data)


        if (terms_conditionsArr.length > 0) {
            var terms_conditionsStr = JSON.parse(JSON.stringify(terms_conditionsArr));
            for (let x in terms_conditionsStr) {
                formData.append("terms_conditions[" + x + "][title]", terms_conditionsArr[x].title);
                formData.append("terms_conditions[" + x + "][description]", terms_conditionsArr[x].description);
            }
        }

        if (facility_offersArr.length > 0) {
            var facility_offersSrt = JSON.parse(JSON.stringify(facility_offersArr));
            for (let x in facility_offersSrt) {
                formData.append("facility_offers[" + x + "][title]", facility_offersArr[x].title);
                formData.append("facility_offers[" + x + "][description]", facility_offersArr[x].description);
                formData.append("facility_offers[" + x + "][photo]", facility_offersArr[x].image);
            }
        }
        // // Display the key/value pairs
        // for (var pair of formData.entries()) {
        //     console.log(pair[0] + ', ' + pair[1]);
        // }

        let result = await addRec(`/item-storages`, formData);

        if (result?.status) {
            SuccessToast(result?.message)
            history.push("/account");
        } else {
            ErrorToast(result?.message)
        }
        setLoading(false)
    };



    const [selectedDates, setSelectedDates] = useState([]);
    const [calendarOpen, setCalendarOpen] = useState(false);
    const datePickerRef = useRef(null);
    const inputRef = useRef(null);

    const handleDateSelect = (date) => {
        let newDates;
        if (selectedDates.some(selectedDate => selectedDate.getTime() === date.getTime())) {
            newDates = selectedDates.filter(selectedDate => selectedDate.getTime() !== date.getTime());
        } else {
            newDates = [...selectedDates, date];
        }
        setSelectedDates(newDates);

        // Format dates to MM/DD/YYYY before setting form value
        const formattedDates = newDates.map(d => formatDateToMMDDYYYY(d));
        setValue('blocked_days', formattedDates);
    };

    const formatDateToMMDDYYYY = (date) => {
        const mm = date.getMonth() + 1; // getMonth() is zero-based
        const dd = date.getDate();
        const yyyy = date.getFullYear();
        return `${mm.toString().padStart(2, '0')}/${dd.toString().padStart(2, '0')}/${yyyy}`;
    };

    const formatDateDisplay = (dates) => {
        return dates?.map(date => formatDateToMMDDYYYY(new Date(date))).join(', ');
    };

    const openDatePicker = () => {
        setCalendarOpen(true);
    };

    const closeDatePicker = () => {
        setCalendarOpen(false);
    };

    const blockedDays = watch('blocked_days');

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (
                inputRef.current &&
                !inputRef.current.contains(event.target) &&
                datePickerRef.current &&
                !datePickerRef.current.contains(event.target)
            ) {
                closeDatePicker();
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);




    // google location
    const mapRef = useRef(null); // Reference to the map DOM element
    const [marker, setMarker] = useState(null); // State for the marker on the map


    useEffect(() => {
        // Initialize Google Places Autocomplete
        const autocomplete = new window.google.maps.places.Autocomplete(
            document.getElementById('locationInput'),
            { types: ['geocode'] }
        );

        // Listen for place selection
        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                console.log("Place details not found for: ", place);
                return;
            }

            // Set latitude and longitude values using react-hook-form
            setValue('latitude', place.geometry.location.lat());
            setValue('longitude', place.geometry.location.lng());
            setValue('location', place.formatted_address);

            if (place?.address_components) {
                place.address_components.forEach(item => {
                    if (item.types.includes("administrative_area_level_3") || item.types.includes("locality")) {
                        setValue("city", item.long_name);
                    }
                    if (item.types.includes("administrative_area_level_1")) {
                        setValue("state", item.long_name);
                    }
                    if (item.types.includes("country")) {
                        setValue("country", item.long_name);
                    }
                    if (item.types.includes("postal_code")) {
                        setValue("pincode", item.long_name);
                    }
                    if (item.types.includes("sublocality_level_1")) {
                        setValue("landmark", item.long_name);
                    }
                });
            }

            // Pan map to selected location and add marker
            if (mapRef.current) {
                const map = new window.google.maps.Map(mapRef.current, {
                    center: place.geometry.location,
                    zoom: 15,
                });

                // Remove the old marker if it exists
                if (marker) {
                    marker.setMap(null);
                }

                // Add new marker for selected location
                const newMarker = new window.google.maps.Marker({
                    position: place.geometry.location,
                    map: map,
                    draggable: true, // Make the marker draggable
                });

                // Update marker state
                setMarker(newMarker);

                // Add event listener to update form values when marker is dragged
                newMarker.addListener('dragend', () => {
                    const newPosition = newMarker.getPosition();
                    setValue('latitude', newPosition.lat());
                    setValue('longitude', newPosition.lng());

                    // Reverse geocode to get address from LatLng
                    const geocoder = new window.google.maps.Geocoder();
                    geocoder.geocode({ location: newPosition }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            const addressComponents = results[0].address_components;

                            setValue('location', results[0].formatted_address);

                            addressComponents.forEach(item => {
                                if (item.types.includes("administrative_area_level_3") || item.types.includes("locality")) {
                                    setValue("city", item.long_name);
                                }
                                if (item.types.includes("administrative_area_level_1")) {
                                    setValue("state", item.long_name);
                                }
                                if (item.types.includes("country")) {
                                    setValue("country", item.long_name);
                                }
                                if (item.types.includes("postal_code")) {
                                    setValue("pincode", item.long_name);
                                }
                                if (item.types.includes("sublocality_level_1")) {
                                    setValue("landmark", item.long_name);
                                }
                            });
                        } else {
                            console.error('Geocode was not successful for the following reason:', status);
                        }
                    });
                });
            }
        });

    }, [setValue, marker]);

    const { fields, append, remove } = useFieldArray({
        control,
        name: 'terms_conditions'
    });

    const { fields: facilityFields, append: appendFacility, remove: removeFacility } = useFieldArray({
        control,
        name: 'facility_offers'
    });

    console.log(getValues());


    console.log("🚀 ~ Step1 ~ errors:", errors)
    const Step1 = useCallback(() => {
        return (
            <Grid container spacing={2}>
                <Grid container spacing={2} alignItems="center">
                    <Grid item xs={4}>
                        <TextField
                            error={!!errors?.listing_type?.message}
                            helperText={errors?.listing_type?.message}
                            {...register('listing_type')}
                            fullWidth
                            label="Name"
                        />
                    </Grid>
                    <Grid item xs={4}>
                        <Controller
                            control={control}
                            name="categories_id"
                            render={({ field: { onChange } }) => {
                                return (
                                    <Autocomplete
                                        key="categories_id"
                                        id="categories_id"
                                        options={CategoryData}
                                        getOptionLabel={(option) => {
                                            return option?.label;
                                        }}
                                        onChange={(_e, data) => onChange(data?.id)}
                                        isOptionEqualToValue={(option) => {
                                            return option ? option?.id : "";
                                        }}
                                        renderInput={(params) => (
                                            <TextField
                                                label="Choose a Category"
                                                variant="outlined"
                                                error={!!errors.categories_id}
                                                helperText={errors.categories_id?.message}
                                                {...params} />
                                        )}
                                    />
                                );
                            }}
                        />
                    </Grid>
                    <Grid item xs={4}>
                        <Controller
                            control={control}
                            name="sub_categories_id"
                            render={({ field: { onChange } }) => {
                                return (
                                    <Autocomplete
                                        key="sub_categories_id"
                                        id="sub_categories_id"
                                        options={subCategories.filter((obj) => obj.categories_id === categories_id_val)}
                                        getOptionLabel={(option) => { return option?.label; }}
                                        onChange={(_e, data) => onChange(data?.id)}
                                        isOptionEqualToValue={(option) => { return option ? option?.id : ""; }}
                                        renderInput={(params) => (
                                            <TextField
                                                label="Choose a Sub-Category"
                                                variant="outlined"
                                                error={!!errors.sub_categories_id}
                                                helperText={errors.sub_categories_id?.message}
                                                {...params} />
                                        )}
                                    />
                                );
                            }}
                        />
                    </Grid>
                    <Grid item xs={12}>
                        <TextField
                            error={!!errors?.description?.message}
                            helperText={errors?.description?.message}
                            {...register('description')}
                            fullWidth
                            multiline
                            rows={3}
                            label="description"
                        />
                    </Grid>
                </Grid>
            </Grid>
        );
    }, [register,
        errors,
        control,
        CategoryData,
        categories_id_val,
        subCategories,
        isDirty
    ]);

    // const Step2 = () => {
    const Step2 = useCallback(() => {
        return (
            <Grid container spacing={2}>
                {/* <h4>Step 2</h4> */}
                <Grid item xs={12}>
                    <div className="form-group">
                        <label htmlFor="locationInput">Location</label>
                        <input
                            type="text"
                            id="locationInput"
                            className="form-control"
                            placeholder="Enter location"
                            {...register('location')}
                        />
                    </div>
                    {location_val && <div style={{ marginTop: '20px' }}>
                        <h5 className='comntitle'>Selected Location</h5>
                        <div ref={mapRef} style={{ height: '400px', width: '100%' }}></div>
                    </div>
                    }
                </Grid>
                <Grid item xs={6}>
                    <InputLabel>Pincode</InputLabel>
                    <TextField
                        error={!!errors?.pincode?.message}
                        helperText={errors?.pincode?.message}
                        {...register('pincode')}
                        fullWidth
                    // label="Pincode"
                    />
                </Grid>
                <Grid item xs={6}>
                    <InputLabel>landmark</InputLabel>
                    <TextField
                        error={!!errors?.landmark?.message}
                        helperText={errors?.landmark?.message}
                        {...register('landmark')}
                        fullWidth
                    // label="landmark"
                    />
                </Grid>

                <Grid item xs={12}>
                    <TagsInput
                        value={getValues("tags")}
                        onChange={(tags) => setValue("tags", tags)}
                        name="Tags"
                        placeHolder="Type & Press enter to add multiple tags."
                    />
                </Grid>

                {/* Exception Details */}
                <Grid item xs={12}>
                    <TextField
                        error={!!errors?.exception_details?.message}
                        helperText={errors?.exception_details?.message}
                        {...register('exception_details')}
                        fullWidth
                        label="Exception Details"
                        multiline
                        rows={2}
                    />
                </Grid>

                {/* Rate */}
                <Grid item xs={6}>
                    <TextField
                        error={!!errors?.rate?.message}
                        helperText={errors?.rate?.message}
                        {...register('rate')}
                        fullWidth
                        label="Rate"
                        type="number"
                    />
                </Grid>

                <Grid item xs={6}>
                    <TextField
                        error={!!errors?.rented_max_allow_days?.message}
                        helperText={errors?.rented_max_allow_days?.message}
                        {...register('rented_max_allow_days')}
                        fullWidth
                        label="Rented Max Allow Days"
                        type="number"
                    />
                </Grid>

                <Grid item xs={6}>
                    <label>Select Blocked Days</label>
                    <Controller
                        name="blocked_days"
                        control={control}
                        render={({ field }) => (
                            <>
                                <input
                                    type="text"
                                    readOnly
                                    value={formatDateDisplay(blockedDays)}
                                    onClick={openDatePicker}
                                    placeholder="Select dates..."
                                    ref={inputRef}
                                    style={{ width: '100%', padding: '10px', boxSizing: 'border-box', marginTop: '10px' }}
                                />
                                {calendarOpen && (
                                    <div ref={datePickerRef} style={{ position: 'absolute' }}>
                                        <DatePicker
                                            selected={null}
                                            onChange={handleDateSelect}
                                            inline
                                            shouldCloseOnSelect={false}
                                            highlightDates={selectedDates.map(date => new Date(date))} // Pass selectedDates as Date objects
                                        />
                                    </div>
                                )}
                            </>
                        )}
                    />
                </Grid>

                <Grid item xs={6}>
                    <InputLabel id="demo-multiple-checkbox-label">Item</InputLabel>
                    <Controller
                        name="item_id"
                        control={control}
                        defaultValue={[]}
                        render={({ field }) => (
                            <Select
                                {...field}
                                labelId="demo-multiple-checkbox-label"
                                multiple
                                fullWidth
                                value={field.value}
                                onChange={(event) => {
                                    setValue("item_id", event.target.value);
                                }}
                                renderValue={(selected) => selected.map(val => ItemList.find(option => option.value === val)?.label).join(', ')}
                            >
                                {ItemList.map((option) => (
                                    <MenuItem key={option.value} value={option.value}>
                                        <Checkbox checked={field.value.indexOf(option.value) > -1} />
                                        <ListItemText primary={option.label} />
                                    </MenuItem>
                                ))}
                            </Select>
                        )}
                    />
                    {errors.item_id && <FormHelperText>{errors.item_id.message}</FormHelperText>}
                </Grid>
            </Grid>
        );
    }, [register,
    control,
        errors,
        location_val,
        isDirty,
    ]);
    // };

    const Step3 = () => {

        return (
            <Grid container spacing={2}>
                {/* <h4>Step 3</h4> */}
                <Grid item xs={12} className='add_remove'>
                    <InputLabel id="demo-multiple-checkbox-label">Facility Offers</InputLabel>
                    {facilityFields.map((item, index) => (
                        <Grid container spacing={2} key={item.id} style={{ marginBottom: '16px' }}>
                            <Grid item xs={12}>
                                <Controller
                                    name={`facility_offers[${index}].title`}
                                    control={control}
                                    render={({ field }) => (
                                        <TextField
                                            label={`Title ${index + 1}`}
                                            fullWidth
                                            {...field}
                                        />
                                    )}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <Controller
                                    name={`facility_offers[${index}].description`}
                                    control={control}
                                    render={({ field }) => (
                                        <TextField
                                            label={`Description ${index + 1}`}
                                            fullWidth
                                            multiline
                                            rows={4}
                                            {...field}
                                        />
                                    )}
                                />
                            </Grid>
                            <Grid item xs={6}>
                                <div className='text_area_box'>
                                    <Controller
                                        name={`facility_offers[${index}].image`}
                                        control={control}
                                        render={({ field }) => (
                                            <TextField
                                                label={`Image ${index + 1}`}
                                                fullWidth
                                                type="file"
                                                onChange={(e) => {
                                                    field.onChange(e.target.files[0]); // Set the selected file to the form state
                                                }}
                                                inputProps={{
                                                    accept: 'image/*', // Allow only image files
                                                }}
                                                InputLabelProps={{
                                                    shrink: true,
                                                }}
                                            />
                                        )}
                                    />
                                    <Button className='black_Btn'
                                        variant="contained"
                                        color="secondary"
                                        onClick={() => removeFacility(index)}
                                    >
                                        <i class="fas fa-trash-alt"></i>  {/* Remove Facility Offer */}
                                    </Button>
                                </div>
                            </Grid>

                        </Grid>
                    ))}
                    <Button className='yellow_Btn'
                        variant="contained"
                        color="primary"
                        onClick={() => appendFacility({ title: '', description: '', image: '' })}
                        style={{ marginBottom: '16px' }}
                    >
                        Add Facility Offer
                    </Button>
                </Grid>

                <Grid item xs={12} className='add_remove'>

                    <InputLabel id="demo-multiple-checkbox-label">Terms and Conditions</InputLabel>
                    {fields.map((item, index) => (
                        <Grid container spacing={2} key={item.id} style={{ marginBottom: '16px' }}>
                            <Grid item xs={12}>
                                <Controller
                                    name={`terms_conditions[${index}].title`}
                                    control={control}
                                    render={({ field }) => (
                                        <TextField
                                            label={`Title ${index + 1}`}
                                            fullWidth
                                            {...field}
                                        />
                                    )}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <div className='text_area_box'>
                                    <Controller
                                        name={`terms_conditions[${index}].description`}
                                        control={control}
                                        render={({ field }) => (
                                            <TextField
                                                label={`Description ${index + 1}`}
                                                fullWidth
                                                multiline
                                                rows={4}
                                                {...field}
                                            />
                                        )}
                                    />
                                    <Button className='black_Btn'
                                        variant="contained"
                                        color="secondary"
                                        onClick={() => remove(index)}
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </Button>
                                </div>
                            </Grid>
                            {/* <Grid item xs={2}>

                    </Grid> */}
                        </Grid>
                    ))}
                    <Button className='yellow_Btn'
                        variant="contained"
                        color="primary"
                        onClick={() => append({ title: '', description: '' })}
                        style={{ marginBottom: '16px' }}
                    >
                        Add Term
                    </Button>
                </Grid>
            </Grid>
        );
    };



    const Step4 = () => {
        return (
            <Grid container spacing={2}>
                {/* <h4>Step 3</h4> */}
                <Grid item xs={12}>
                    <div className='upload_box'>
                        <FileUploadComponent ButtonName="Default Image" isSingle={true} FileUploadChange={(data) => setValue("default_storage_photo", data)} />

                        <FileUploadComponent ButtonName="Multiple Image" isSingle={false} FileUploadChange={(data) => setValue("storage_photos", data)} />
                    </div>
                </Grid>
            </Grid>
        );
    };





    const steps = [<Step1 />, <Step2 />, <Step3 />, <Step4 />];




    // const methods = useForm({
    //     defaultValues: {
    //         listing_type: "",
    //         categories_id: null,
    //         sub_categories_id: null,
    //         location: "",


    //         description: "",
    //         country: "",
    //         state: "",
    //         city: "",
    //         pincode: "",
    //         landmark: "",
    //         latitude: "",
    //         longitude: "",
    //         tags: [],

    //         newTag: '',

    //         exception_details: "",
    //         rate: "",
    //         rented_max_allow_days: 0,
    //         blocked_days: [],
    //         item_id: [],
    //         default_storage_photo: [],
    //         storage_photos: [],

    //         terms_conditions: [],
    //         facility_offers: []
    //     },
    //     // resolver: yupResolver(AddItemValidation),
    //     resolver: yupResolver([step1ValidationSchema, step2ValidationSchema, step3ValidationSchema][step]),
    // });

    const handleNext = (data) => {
        if (step < steps.length - 1) {
            setStep(step + 1);
        } else {
            console.log(data);
            // Handle final submission
        }
    };

    const handleBack = () => {
        if (step > 0) {
            setStep(step - 1);
        }
    };





    return (
        <FormProvider>
            <Container className='add_list_box'>
                <h4>
                    {StepsLabel.map((item, e) => (
                        <a key={item}>
                            <span>{step > e ? <i class="far fa-check"></i> : e + 1}</span>
                            <span className="labe">{item} </span>
                        </a>
                    ))}
                </h4>
                <form onSubmit={handleSubmit(handleNext)}>
                    {steps[step]}
                    <Grid container spacing={2}>
                        <Grid item xs={6}>
                            <Button
                                type="button"
                                variant="contained"
                                color="primary"
                                disabled={step === 0}
                                onClick={handleBack}
                                fullWidth
                            >
                                Back
                            </Button>
                        </Grid>
                        <Grid item xs={6}>
                            <Button
                                type="submit"
                                variant="contained"
                                color="primary"
                                fullWidth
                            >
                                {step === steps.length - 1 ? 'Submit' : 'Next'}
                            </Button>
                        </Grid>
                    </Grid>
                </form>
            </Container>
        </FormProvider>
    );
};

export default AddItem;
