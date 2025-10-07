import AccountCircleIcon from "@mui/icons-material/AccountCircle";
import MenuIcon from "@mui/icons-material/Menu";
import { Avatar, Box, IconButton, Menu, MenuItem } from "@mui/material";
import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { useHistory, useLocation } from "react-router-dom";
import { googleLogout, useGoogleLogin } from '@react-oauth/google';

import { addRec } from "../../common/common";
import { logoutSuccess, OpenLogin } from "../../store/actions";
import AuthModal from "./AuthModal";
import axios from "axios";

let Arr = ["/account"]

const ProfileMenu = () => {
  const dispatch = useDispatch();
  const history = useHistory();

  let GoogleToken = localStorage.getItem("Google_Access_Token")

  const { token } = useSelector((state) => state.user);
  const location = useLocation();

  const { pathname } = location

  useEffect(() => {

    if (Arr.includes(pathname)) {
      setIsMyAccount(true)
    } else {
      setIsMyAccount(false)
    }

  }, [pathname])



  const [anchorEl, setAnchorEl] = useState(null);
  const [deleteLoading, setDeleteLoading] = useState(false);
  const [IsMyAccount, setIsMyAccount] = useState(false);
  const [ModalProps, setModalProps] = useState({ reg: false, modal: false });

  // const [user, setUser] = useState(null);
  const [profile, setProfile] = useState([]);

  useEffect(
    () => {
      if (GoogleToken) {
        axios
          .get(`https://www.googleapis.com/oauth2/v1/userinfo?access_token=${GoogleToken}`, {
            headers: {
              Authorization: `Bearer ${GoogleToken}`,
              Accept: 'application/json'
            }
          })
          .then((res) => {
            setProfile(res.data);
          })
          .catch((err) => console.log(err));
      }
    },
    [GoogleToken]
  );

  const handleMenuOpen = (event) => {
    setAnchorEl(event.currentTarget);
  };

  const handleMenuClose = () => {
    setAnchorEl(null);
  };

  const logout = async () => {
    setDeleteLoading(true);

    if (GoogleToken) {
      googleLogout();
      setProfile(null);
      localStorage.removeItem("Google_Access_Token");
    }
    if (token) {

      let result = await addRec(`/logout`);
      if (result?.status) {
        localStorage.removeItem("token");
        dispatch(logoutSuccess());
        history.push("/home");
        setAnchorEl(null);
      }
      setDeleteLoading(false);
    }
  };

  const OpenLoginModal = async (Registration) => {
    setAnchorEl(null);
    // history.push("/login");
    if (Registration) {
      dispatch(OpenLogin({ reg: true, modal: true }))
    } else {
      dispatch(OpenLogin({ reg: false, modal: true }))
    }
  };
  const closedModal = async () => {
    setAnchorEl(null);
    dispatch(OpenLogin({ reg: false, modal: false }))
  };



  const login = useGoogleLogin({
    onSuccess: (codeResponse) => {
      // setUser(codeResponse);
      dispatch(OpenLogin({ reg: false, modal: false }))
      localStorage.setItem("Google_Access_Token", codeResponse?.access_token);
    },
    onError: (error) => console.log('Login Failed:', error)
  });

  return (
    <>
      <Box className={`Login_Btn ${IsMyAccount ? "afterLogin" : ""}`} sx={{ position: "absolute" }}>
        <IconButton onClick={handleMenuOpen}>
          <Avatar
            sx={{ width: "100%", height: "100%", background: "transparent" }}
          >
            <Box
              sx={{
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
              }}
            >
              <MenuIcon />
              <AccountCircleIcon />
            </Box>
          </Avatar>
        </IconButton>
        <Menu
          anchorEl={anchorEl}
          open={Boolean(anchorEl)}
          onClose={handleMenuClose}
          anchorOrigin={{
            vertical: "bottom",
            horizontal: "right",
          }}
          transformOrigin={{
            vertical: "top",
            horizontal: "right",
          }}
          disableScrollLock={true}
          sx={{
            position: "absolute", // Absolute position for the menu
          }}
        >
          {GoogleToken && <>
            <MenuItem >Google User : {profile?.given_name}</MenuItem>
          </>}
          {!token && <MenuItem onClick={() => OpenLoginModal(true)}>Sign up</MenuItem>}
          {token &&<MenuItem onClick={() => history.push("/account")}>My account</MenuItem>}
          {token &&<MenuItem>Contact</MenuItem>}
          {/* <MenuItem onClick={() => login()}>Google Login</MenuItem> */}
          {!token && <MenuItem onClick={() => OpenLoginModal(false)}>Log in</MenuItem>}
          {(token || GoogleToken) && <MenuItem onClick={logout}>
            {deleteLoading && <i className="fa fa-spinner fa-spin"></i>}Logout
          </MenuItem>}
        </Menu>
      </Box>
      <AuthModal GoogleLogin={() => login()} />
    </>
  );
};

export default ProfileMenu;
