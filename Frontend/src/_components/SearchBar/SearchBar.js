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
import { useHistory } from 'react-router-dom';

const SearchBar = ({ SearchReq }) => {
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
      role: "",
      dateRange: [],
    },
  });

  const history = useHistory();

  const categories_id_val = useWatch({
    control,
    name: "categories_id",
  });
  const location_val = useWatch({
    control,
    name: "location",
  });

  const [dateRange, setDateRange] = useState([
    { startDate: null, endDate: null, key: "selection" },
  ]);

  const [, setShowDateRange] = useState(false);
  const [anchorEl, setAnchorEl] = useState(null);
  const [loading, setLoading] = useState(false);

  const handleSelect = (ranges) => {
    setValue("date_from", ranges?.selection?.startDate.toDateString());
    setValue("date_to", ranges?.selection?.endDate.toDateString());
    setValue("dateRange", [ranges.selection]);
    setDateRange([ranges.selection]);
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

  const Submit = async (data) => {
    // setLoading(true);
    // let result = await addRec(`/get-item-storage`, data);
    // if (result?.status) {
    //   if (result?.responseData?.length === 0) {
    //     ErrorToast(result?.message);
    //     searchData([]);
    //   } else {
    //     searchData(result?.responseData);
    //   }
    // }
    // setLoading(false);
    SearchReq(data);
  };

  useEffect(() => {
    // Get_Category_SubCategory();
  }, []);

  const RedirectPage = async () => {
    history.push("/list-item");
  };


  return (
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
            <Button className="Browse_Btn" onClick={RedirectPage}>Browse <small>Item/Storage</small> </Button>
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
          <Grid item xs={12} md={2}>
            <Box>
              <Button
                variant="text"
                onClick={handleDateClick}
                sx={{ width: "100%", textAlign: "left" }}
              >
                {dateRange[0]?.startDate ? dateRange[0]?.startDate?.toDateString() : "Check in"}
              </Button>
            </Box>
          </Grid>
          <Grid item xs={12} md={2}>
            <Box>
              {/* <Typography variant="subtitle2">Check out</Typography> */}
              <Button
                variant="text"
                onClick={handleDateClick}
                sx={{ width: "100%", textAlign: "left" }}
              >
                {dateRange[0]?.endDate ? dateRange[0]?.endDate?.toDateString() : "Check out"}
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
  );
};

export default SearchBar;