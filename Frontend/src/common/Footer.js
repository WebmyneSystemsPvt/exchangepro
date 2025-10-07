import React from 'react';

import { useHistory } from 'react-router-dom';
import leaf from '../asset/images/leaf.png';


const Footer = () => {

    const history = useHistory();

    const RedirectPage = (url) => {
        history.push(url);
    }

    return (
        <footer>
            <div className='container'>
                <div className='ft'>
                    <img src={leaf} alt='footer logo' className='ft_logo' />
                    <ul className='ft_menu'>
                        <li>
                            <a href='javascript:void(0);' onClick={() => RedirectPage("/home")}>HOME</a>
                        </li>
                        <li>
                            <a href='javascript:void(0);' onClick={() => RedirectPage("/list-item")}>BROWSE</a>
                        </li>
                        <li>
                            <a href='javascript:void(0);' onClick={() => RedirectPage("/terms")}>TERMS</a>
                        </li>
                        <li>
                            <a href='javascript:void(0);' onClick={() => RedirectPage("/policy")}>PRIVACY POLICY</a>
                        </li>
                        <li>
                            <a href='javascript:void(0);' onClick={() => RedirectPage("/login")}>SELLER LOGIN</a>
                        </li>
                        <li>
                            <a href='javascript:void(0);' onClick={() => RedirectPage("/contact")}>CONTACT</a>
                        </li>
                    </ul>
                </div>
                <div className='copy'>
                    <span>Copyright © 2024. All right reserve.</span>
                    <div className='ft_social'>
                        <a href='' class="fab fa-facebook-f"></a>
                        <a href='' class="fab fa-instagram"></a>
                        <a href='' class="fab fa-twitter"></a>
                        <a href='' class="fab fa-youtube"></a>
                    </div>
                </div>
            </div>
        </footer>
    )
}

export default Footer
