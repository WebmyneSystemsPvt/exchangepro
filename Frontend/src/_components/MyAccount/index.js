import React, { useEffect, useState } from 'react'
import { postFetchListing } from '../../common/common';
import { PageLoader } from '../../common/Loader';
import CardComp from '../../common/Card';
import { useHistory } from 'react-router-dom';
import { ErrorToast, SuccessToast } from '../../utils/Toaster';

const MyAccount = () => {

  const history = useHistory();

  useEffect(() => {
    Get_MyItemList();
  }, []);

  const [Loader, setLoader] = useState(false);
  const [MyItemList, setMyItemList] = useState([]);

  const Get_MyItemList = async () => {
    setLoader(true)
    let resultItem = await postFetchListing(`/my-item-storage`);
    if (resultItem?.status) {
      if (resultItem?.responseData?.data.length > 0) {
        setMyItemList(resultItem?.responseData?.data);
      } else {
        SuccessToast(resultItem?.message)
      }
    } else {
      ErrorToast(resultItem?.message)
    }
    setLoader(false)
  };

  const RedirectPage = (item) => {
    history.replace({ ...history.location, state: undefined });
    history.push(`/item-details/${item?.id}`);
  }

  return (
    <div className="container">
      <div className="row">
        <div className="col-lg-3">
          <aside className="sidebar">
            <h3>Booking</h3>
            <div className="store_item_list">
              <a href=''>
                <span>Current Booking</span>
              </a>
              <a href=''>
                <span>Past Borrows</span>
              </a>
              <a href=''>
                <span>Canceled Bookings</span>
              </a>
            </div>
            <h3>Support</h3>
            <div className="store_item_list">
              <a href=''>
                <span>Help Center</span>
              </a>
              <a href=''>
                <span>Contact Support</span>
              </a>
              <a href=''>
                <span>FAQs</span>
              </a>
            </div>
          </aside>
        </div>
        <div className="col-lg-9">
          {Loader
            ? <PageLoader />

            : <ul className='list-view'>
              {MyItemList.length > 0
                && MyItemList.map((item) => (
                  <li>
                    <CardComp item={item} RedirectPage={RedirectPage} />
                  </li>
                )
                )}
            </ul>
          }
        </div>
      </div>
    </div>
  )
}

export default MyAccount
