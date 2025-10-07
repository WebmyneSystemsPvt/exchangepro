import { Search as SearchIcon } from "@mui/icons-material";
import {
  Box,
  Button,
  Container,
  Grid,
  IconButton,
  InputAdornment,
  Popover,
  TextField,
  Typography
} from "@mui/material";
import _ from "lodash";
import React, { useEffect, useMemo, useState } from "react";
import { DateRangePicker } from "react-date-range";
import { useForm, useWatch } from "react-hook-form";

import "react-date-range/dist/styles.css";
import "react-date-range/dist/theme/default.css";
import { useHistory } from 'react-router-dom';
import { addRec, fetchListing } from "../../common/common";
import { ErrorToast } from "../../utils/Toaster";

const SearchBar = ({ searchData }) => {
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
      location: "",
      date_from: "",
      date_to: "",
      role: "",
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
    { startDate: new Date(), endDate: new Date(), key: "selection" },
  ]);
  const [, setShowDateRange] = useState(false);
  const [anchorEl, setAnchorEl] = useState(null);
  const [CategoriesList, setCategoriesList] = useState([]);
  const [SubCategoriesList, setSubCategoriesList] = useState([]);
  const [SubCategoriesMainList, setSubCategoriesMainList] = useState([]);
  const [loading, setLoading] = useState(false);

  const handleSelect = (ranges) => {
    setValue("date_from", ranges?.selection?.startDate.toDateString());
    setValue("date_to", ranges?.selection?.endDate.toDateString());
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
    setLoading(true);
    let result = await addRec(`/get-item-storage`, data);
    if (result?.status) {
      if (result?.responseData?.data?.length === 0) {
        ErrorToast(result?.message);
        searchData([]);
      } else {
        searchData(result?.responseData?.data);
      }
    }
    setLoading(false);
  };

  useEffect(() => {
    Get_Category_SubCategory();
  }, []);
  useEffect(() => {
    if (categories_id_val) {
      setSubCategoriesList(
        SubCategoriesMainList.filter(
          (cat) => cat.categories_id === categories_id_val
        )
      );
    } else {
      setSubCategoriesList(SubCategoriesMainList);
    }
  }, [categories_id_val, SubCategoriesMainList]);

  const Get_Category_SubCategory = async () => {
    let resultCategory = await fetchListing(`/categories`);
    if (resultCategory?.status) {
      setCategoriesList(resultCategory?.responseData);
    }
    let resultSubCategory = await fetchListing(`/sub-categories`);
    if (resultSubCategory?.status) {
      setSubCategoriesList(resultSubCategory?.responseData);
      setSubCategoriesMainList(resultSubCategory?.responseData);
    }
  };
  const RedirectPage = async () => {
    history.push("/add-item");
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
                name="location"
                {...register("location")}
                InputProps={{
                  disableUnderline: true,
                  startAdornment: (
                    <InputAdornment position="start">
                      <SearchIcon />
                    </InputAdornment>
                  ),
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
                  startAdornment: (
                    <InputAdornment position="start">
                      <SearchIcon />
                    </InputAdornment>
                  ),
                }}
              />
            </Box>
          </Grid>
          {/* <Grid item xs={12} md={2}>
            <FormControl fullWidth>
              <InputLabel id="category-label">Category</InputLabel>
              <Select
                labelId="category-label"
                id="category-select"
                name="categories_id"
                {...register("categories_id", {
                  onChange: (e) => {
                    if (e.target.value !== categories_id_val)
                      setValue("sub_categories_id", "");
                  },
                })}
                value={categories_id_val}
                label="Category"
                endAdornment={
                  categories_id_val && (
                    <IconButton
                      onClick={() => {
                        setValue("categories_id", "");
                        setValue("sub_categories_id", "");
                      }}
                      size="small"
                    >
                      <ClearIcon />
                    </IconButton>
                  )
                }
              >
                {CategoriesList.map((cat) => (
                  <MenuItem key={cat.id} value={cat.id}>
                    {cat.name}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>
          <Grid item xs={12} md={2}>
            <FormControl fullWidth>
              <InputLabel id="subcategory-label">Subcategory</InputLabel>
              <Select
                labelId="subcategory-label"
                id="subcategory-select"
                name="sub_categories_id"
                {...register("sub_categories_id")}
                label="Subcategory"
                value={sub_categories_id_val}
                endAdornment={
                  sub_categories_id_val && (
                    <IconButton
                      onClick={() => {
                        setValue("sub_categories_id", "");
                      }}
                      size="small"
                    >
                      <ClearIcon />
                    </IconButton>
                  )
                }
              >
                {SubCategoriesList?.map((subcat, index) => (
                  <MenuItem key={index} value={subcat.id}>
                    {subcat.name}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid> */}
          <Grid item xs={12} md={2}>
            <Box>
              <Typography variant="subtitle2">Check in</Typography>
              <Button
                variant="text"
                onClick={handleDateClick}
                sx={{ width: "100%", textAlign: "left" }}
              >
                {dateRange[0].startDate.toDateString()}
              </Button>
            </Box>
          </Grid>
          <Grid item xs={12} md={2}>
            <Box>
              <Typography variant="subtitle2">Check out</Typography>
              <Button
                variant="text"
                onClick={handleDateClick}
                sx={{ width: "100%", textAlign: "left" }}
              >
                {dateRange[0].endDate.toDateString()}
              </Button>
            </Box>
          </Grid>
        </Grid>
        <IconButton className="SearchIcon_Btn"
          onClick={handleSubmit(Submit)}
          disabled={loading}
          sx={{ color: "white", ml: 2 }}
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
            showSelectionPreview={true}
            moveRangeOnFirstSelection={false}
            months={1}
            direction="horizontal"
          />
        </Popover>
      </Box>
    </Container>
  );
};

export default SearchBar;