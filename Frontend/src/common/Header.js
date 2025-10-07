import React, { useEffect, useState } from "react";
import { useSelector } from "react-redux";
import { useHistory, useLocation } from "react-router-dom";

import Logo from "../asset/images/logo.png";

const HideAddBtn = ["account"];

const Header = () => {
  const history = useHistory();
  const location = useLocation();

  const { token, info } = useSelector((state) => state.user);
  const { pathname } = location;

  const [HideBtn, setHideBtn] = useState(false);

  useEffect(() => {
    if (!token) {
      RedirectPage("/home");
    }
  }, [token, info]);

  useEffect(() => {
    let Url = pathname.replace(/\//g, "");
    if (HideAddBtn.includes(Url)) {
      if (token && info?.role_id === 3) {
        setHideBtn(true);
      } else {
        setHideBtn(false);
      }
    } else {
      setHideBtn(false);
    }
  }, [pathname, info, token]);

  const RedirectPage = (url) => {
    history.push(url);
  };

  return (
    <header>
      <div className="container">
        <div className="second_header">
          <div className="inner_logo">
            <a href="javascript:void(0);" onClick={() => RedirectPage("/home")}>
              <img src={Logo} alt="Logo" />
            </a>
          </div>
          <div className="head_link">

            <a href="javascript:void(0);" onClick={() => RedirectPage("/account")} className={pathname === "/account" ? "active" : ""}>My Listing</a>

            {/* <a href="javascript:void(0);">Wishlist</a> */}
            <a href="javascript:void(0);">Profile</a>
            {HideBtn && (
              <a
                href="javascript:void(0);"
                onClick={() => RedirectPage("/add-item")}
                className="seller_add_btn"
              >
                <i class="fas fa-plus-octagon"></i>New Listing
              </a>
            )}
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;
