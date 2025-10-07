import { Search as SearchIcon } from "@mui/icons-material";
import {
  Box,
  Button,
  Container,
  Grid,
  IconButton,
  Popover,
  TextField
} from "@mui/material";
import _ from "lodash";
import React, { useEffect, useMemo, useState } from "react";
import { DateRangePicker } from "react-date-range";
import { useForm, useWatch } from "react-hook-form";

import "react-date-range/dist/styles.css";
import "react-date-range/dist/theme/default.css";
import { useDispatch, useSelector } from "react-redux";
import { useHistory } from 'react-router-dom';
import Logo from '../../asset/images/logo.png';
import { SearchProperties } from "../../store/actions/common";

const ListSearch = ({ SearchReq, SubmitSearch }) => {

  const {
    register,
    handleSubmit,
    // getValues,
    setValue,
    control,
    // formState: { errors },
    // reset,
  } = useForm({
    defaultValues: {
      categories_id: "",
      sub_categories_id: "",
      listing_type: "",
      location: "",
      date_from: "",
      date_to: "",
      price_min: "",
      price_max: "",
      role: "",
      dateRange: [],
    },
  });
  const dispatch = useDispatch();
  const history = useHistory();

  const location_val = useWatch({
    control,
    name: "location",
  });

  const { ResetSearch } = useSelector((state) => state.common);

  const [dateRange, setDateRange] = useState([
    { startDate: null, endDate: null, key: "selection" },
  ]);
  const [, setShowDateRange] = useState(false);
  const [anchorEl, setAnchorEl] = useState(null);
  const [loading, setLoading] = useState(false);

  const handleSelect = (ranges) => {
    setValue("date_from", ranges?.selection?.startDate.toDateString());
    setValue("date_to", ranges?.selection?.endDate.toDateString());

    setDateRange([ranges.selection]);
    setValue("dateRange", [ranges.selection]);

  };

  const handleDateClick = (event) => {
    setAnchorEl(event.currentTarget);
    setShowDateRange(true);
  };

  const handlePopoverClose = () => {
    setAnchorEl(null);
    setShowDateRange(false);
  };

  const open = Boolean(anchorEl);
  const id = open ? "simple-popover" : undefined;

  const debouncedSearch = _.debounce(() => {
    setTimeout(() => {
      // console.log('API call with:', searchQuery);
    }, 500);
  }, 1000); // Adjust the debounce delay as needed (1000ms = 1 second)

  const handleSearch = useMemo(() => debouncedSearch, [debouncedSearch]);

  useEffect(() => {
    handleSearch(location_val);
  }, [handleSearch, location_val]);

  useEffect(() => {
    if (ResetSearch) {
      dispatch(SearchProperties(false))
      setValue("categories_id", "")
      setValue("sub_categories_id", "")
      setValue("listing_type", "")
      setValue("location", "")
      setValue("date_from", "")
      setValue("date_to", "")
      setValue("role", "")
      setDateRange([
        { startDate: null, endDate: null, key: "selection" },
      ])
    }
  }, [ResetSearch]);

  useEffect(() => {
    if (SearchReq) {
      setValue("categories_id", SearchReq?.categories_id)
      setValue("sub_categories_id", SearchReq?.sub_categories_id)
      setValue("listing_type", SearchReq?.listing_type)
      setValue("location", SearchReq?.location)
      setValue("date_from", SearchReq?.date_from)
      setValue("date_to", SearchReq?.date_to)
      setValue("price_min", SearchReq?.price_min)
      setValue("price_max", SearchReq?.price_max)
      setValue("role", SearchReq?.role)
      if (SearchReq?.dateRange?.length > 0) setDateRange(SearchReq?.dateRange)
    }
  }, [SearchReq]);

  const Submit = async (data) => {
    SubmitSearch(data)
  };


  const RedirectPage = async (url) => {
    history.push(url);
  };


  return (
    <header>
      <div className="container">
        <div className="row justify-content-between">
          <div className="col-xl-2 col-md-3 col-6">
            <a href='javascript:void(0);' onClick={() => RedirectPage("/home")}>
              <img src={Logo} alt="logo" />
            </a>
          </div>
          <div className="col-xl-7">
            <Container>
              <Box
                sx={{
                  display: "flex",
                  alignItems: "center",
                  padding: "5px 20px",
                  borderRadius: "999px",
                  boxShadow: 1,
                  backgroundColor: "white",
                }}
              >
                <Grid container alignItems="center" spacing={2}>
                  <Grid item xs={12} md={2}>
                    <Button className="Browse_Btn" onClick={() => RedirectPage("/list-item")}>Browse <small>Item/Storage</small> </Button>
                  </Grid>
                  <Grid item xs={12} md={3}>
                    <Box>
                      <TextField
                        variant="standard"
                        placeholder="Search what you want"
                        fullWidth
                        name="listing_type"
                        {...register("listing_type")}
                        InputProps={{
                          disableUnderline: true,
                        }}
                      />
                    </Box>
                  </Grid>
                  <Grid item xs={12} md={3}>
                    <Box>
                      <TextField
                        variant="standard"
                        placeholder="By Location"
                        fullWidth
                        name="location"
                        {...register("location")}
                        InputProps={{
                          disableUnderline: true,
                        }}
                      />
                    </Box>
                  </Grid>
                  <Grid item xs={12} md={3}>
                    <Box>
                      <Button
                        variant="text"
                        onClick={handleDateClick}
                        sx={{ width: "100%", textAlign: "left" }}
                      >
                        {dateRange && dateRange[0]?.startDate ? dateRange[0]?.startDate?.toDateString() : "Check in"}
                      </Button>
                    </Box>
                  </Grid>
                  <Grid item xs={12} md={3}>
                    <Box>
                      {/* <Typography variant="subtitle2">Check out</Typography> */}
                      <Button
                        variant="text"
                        onClick={handleDateClick}
                        sx={{ width: "100%", textAlign: "left" }}
                      >
                        {dateRange && dateRange[0]?.endDate ? dateRange[0]?.endDate?.toDateString() : "Check out"}
                      </Button>
                    </Box>
                  </Grid>
                </Grid>
                <IconButton className="SearchIcon_Btn"
                  onClick={handleSubmit(Submit)}
                  disabled={loading}
                // sx={{ backgroundColor: "#FF385C", color: "white", ml: 2 }}
                >
                  {loading ? <i className="fa fa-spinner fa-spin"></i> : <SearchIcon />}
                </IconButton>
                <Popover
                  id={id}
                  open={open}
                  anchorEl={anchorEl}
                  onClose={handlePopoverClose}
                  anchorOrigin={{
                    vertical: "bottom",
                    horizontal: "left",
                  }}
                  transformOrigin={{
                    vertical: "top",
                    horizontal: "left",
                  }}
                >
                  <DateRangePicker
                    ranges={dateRange}
                    onChange={handleSelect}
                    months={1}
                    endDatePlaceholder="Continuo"
                    rangeColors={['#BF4804']}
                    direction="horizontal"
                    className="custom-calendar"
                    classNames={{
                      calendarWrapper: 'custom-calendar-wrapper',
                      monthAndYearWrapper: 'custom-calendar-header',
                      day: 'custom-calendar-day',
                      selected: 'custom-calendar-day-selected',
                      weekday: 'custom-calendar-weekday',
                    }}
                  />
                </Popover>
              </Box>
            </Container>
          </div>
          <div className="col-xl-1"></div>
        </div>
      </div>
    </header>
  );
};

export default ListSearch;