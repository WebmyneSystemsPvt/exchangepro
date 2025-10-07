import React, { useEffect, useState } from "react";
import { useForm, useWatch } from "react-hook-form";
import { useDispatch, useSelector } from 'react-redux';
import { useHistory, useLocation } from "react-router-dom";
import { fetchListing, postFetch, postFetchListing } from "../../common/common";
import { PageLoader } from "../../common/Loader";
import CustomPagination from "../../common/Pagination/CustomPagination";
import { SearchProperties } from "../../store/actions/common";
import { ChangeKeyValue } from "../../utils/utils";
import ListSearch from "../SearchBar/ListSearch";
import PriceRangeFilter from "../SearchBar/PriceRangeFilter";
import CardComp from './../../common/Card';


const ItemList = () => {

  const {
    // register,
    // handleSubmit,
    getValues,
    setValue,
    control,
    formState: { errors },
    reset,
  } = useForm({
    defaultValues: {
      SearchReq: {},
      "page": 1,
      "limit": "10",
      "totalItems": null,
      "totalPages": null
    },
  });


  const SearchReq_val = useWatch({
    control,
    name: "SearchReq",
  });
  const limit_Val = useWatch({
    control,
    name: "limit",
  });
  const page_val = useWatch({
    control,
    name: "page",
  });
  const totalItems = useWatch({
    control,
    name: "totalItems",
  });
  const totalPages = useWatch({
    control,
    name: "totalPages",
  });

  const history = useHistory();
  const Location = useLocation();
  const dispatch = useDispatch();

  const { Coordinates } = useSelector((state) => state.common);
  console.log("🚀 ~ ItemList ~ Coordinates:", Coordinates)

  const { state: ItemData } = Location

  const [searchData, setSearchData] = useState([]);
  const [Loader, setLoader] = useState(false);
  const [activeCate, setActiveCate] = useState(null);
  const [sidebarClass, setSidebarClass] = useState("sidebar");
  const [PriceRange, setPriceRange] = useState({ price_min: 0, price_max: 0 });



  const PriceRangeFunc = (value) => {
    setPriceRange({ price_min: value[0], price_max: value[1] })
    let OldVal = getValues("SearchReq")
    let Req = { ...OldVal, price_min: value[0], price_max: value[1] }
    setValue("SearchReq", Req)
    getItemStorage(Req);
  };

  useEffect(() => {
    if (ItemData?.from === "FromCategory") {

      setActiveCate(ItemData?.itemReq?.id)

      let req = { "sub_categories_id": ItemData?.itemReq?.id }

      setValue("SearchReq", req)
      getItemStorage(req);

    } else if (ItemData?.itemReq) {

      setPriceRange({ price_min: ItemData?.itemReq?.price_min, price_max: ItemData?.itemReq?.price_max })
      setValue("SearchReq", ItemData?.itemReq)

      getItemStorage(ItemData?.itemReq);
    } else {
      getItemStorage();
    }
  }, [ItemData]);

  const getItemStorage = async (item) => {
    setLoader(true)
    let ItemsResult = null
    if (item) {
      item = { ...item, ...Coordinates }
      ItemsResult = await postFetch(`/get-item-storage`, item);
    } else {
      ItemsResult = await postFetch(`/get-item-storage`, Coordinates);
    }
    if (ItemsResult?.status) {
      setValue("limit", ItemsResult?.responseData?.pagination?.limit)
      setValue("page", ItemsResult?.responseData?.pagination?.page)
      setValue("totalItems", ItemsResult?.responseData?.pagination?.totalItems)
      setValue("totalPages", ItemsResult?.responseData?.pagination?.totalPages)
      setSearchData(ChangeKeyValue(ItemsResult?.responseData?.data, "item_name"));
    }
    setLoader(false)
  };


  const RedirectPage = async (item) => {
    // remove reqFIlter
    history.replace({ ...history.location, state: undefined });

    history.push(`/item-details/${item?.id}`);
  };

  useEffect(() => {
    Get_Category();
  }, []);

  const [CategoriesList, setCategoriesList] = useState([]);

  const Get_Category = async () => {
    let resultCategory = await fetchListing(`/categories`);
    if (resultCategory?.status) {
      setCategoriesList(resultCategory?.responseData);
    }
  };

  const handlePageChange = (newPageNumber) => {
    setValue("page", newPageNumber)
    let Val = getValues()
    let reqLimit = Val?.limit
    let req = Val?.SearchReq
    getItemStorage({ ...req, "limit": reqLimit, "page": newPageNumber })
  };

  const handlePageSizeChange = (newPageSize) => {
    setValue("limit", newPageSize)
    setValue("page", 1)
    let Val = getValues()
    let req = Val?.SearchReq
    getItemStorage({ ...req, "limit": newPageSize, "page": 1 })
  };

  const handleFilterButtonClick = () => {
    setSidebarClass(prevClass => prevClass === "sidebar" ? "sidebar show" : "sidebar");
  };

  const ResetFunc = () => {
    getItemStorage();
    setValue("SearchReq", {})
    setActiveCate(null)
    history.replace({ ...history.location, state: undefined });
    dispatch(SearchProperties(true))
  }

  const CategoryChange = (S_Item) => {
    let OldVal = getValues("SearchReq")
    let Req = { ...OldVal, "sub_categories_id": S_Item?.id }
    setValue("SearchReq", Req)
    getItemStorage(Req);
    setActiveCate(S_Item.id)
  };


  return (

    <div className="LIST_PAGE">
      <ListSearch
        SearchReq={SearchReq_val}
        SubmitSearch={(req) => {
          getItemStorage(req);
          setValue("SearchReq", req)
        }} />
      <section className="headerPadding List_Items">
        <div className="container">
          <div className="row">
            <div className="col-12">
              <div className="Sort_by">
                {searchData.length > 0 &&
                  <>
                    <small>{searchData.length} Listings Found</small>
                    <div>
                      <span>Sort by</span>
                      <select>
                        <option>Recommended</option>
                        <option>Recommended  1</option>
                        <option>Recommended 2</option>
                      </select>
                    </div>
                  </>
                }
              </div>
            </div>
          </div>
          <div className="row">
            <div className="col-lg-3 col-md-4">
              <button className="mob_fiterbtn" onClick={handleFilterButtonClick}>Filter</button>
              <aside className={sidebarClass}>
                <button className="fas fa-times sidebar_close" onClick={handleFilterButtonClick}></button>
                {CategoriesList.length > 0 && CategoriesList.map((item) => (
                  <>
                    <h3>{item?.name}</h3>
                    <div className="store_item_list">
                      {item?.sub_categories?.length > 0 && item?.sub_categories?.map((S_Item) => {
                        return (
                          <a
                            href='javascript:void(0);'
                            className={activeCate === S_Item.id ? "active" : ""}
                            onClick={() => CategoryChange(S_Item)}
                          >
                            <img src={S_Item?.photo} alt='Rentals1' />
                            <span >{S_Item?.name}</span>
                          </a>
                        )
                      })}
                    </div>
                  </>
                )
                )}
                <div className="filterprice">
                  <h3>Price Filter</h3>
                  <PriceRangeFilter PriceRange={PriceRangeFunc} Data={PriceRange} />
                  <button className="Outline_Btn" onClick={() => ResetFunc()}>Reset Filter</button>
                </div>
              </aside>
            </div>
            <div className="col-lg-9 col-md-8">

              {Loader
                ? <PageLoader />

                : <ul className='list-view'>
                  {searchData.length > 0
                    && searchData.map((item) => (
                      <li>
                        <CardComp item={item} RedirectPage={RedirectPage} />
                      </li>
                    )
                    )}
                </ul>
              }
              <CustomPagination
                pageNumber={page_val}
                pageSize={limit_Val}
                totalItems={totalItems}
                totalPages={totalPages}
                onPageChange={handlePageChange}
                onPageSizeChange={handlePageSizeChange}
              />
            </div>
          </div>
        </div>
      </section>

    </div>
  );
};

export default ItemList;


