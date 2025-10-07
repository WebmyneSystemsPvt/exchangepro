import React from 'react'
import Header from '../common/Header';
import { Link } from "react-router-dom";

const NotFoundPage = () => {
  return (
    <div>
      <Header />
      <main id="main-container">
        <div className="content">
          <div className="block block-rounded">
            <div
              className="d-flex flex-column justify-content-center align-items-center flex-fill p-5"
              style={{ minHeight: "80vh" }}
            >

              <>
                <h1
                  style={{
                    fontSize: "8rem",
                    marginBottom: "0",
                  }}
                >
                  404
                </h1>
                <h3 className="text-muted mb-4" style={{ fontSize: "2rem" }}>
                  Page Not Found
                </h3>
                <div className="d-flex align-items-center">
                  <Link to="/home" className="btn btn-primary">
                    <i className="fa fa-home"></i> &nbsp;Home
                  </Link>
                </div>
              </>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
};

export default NotFoundPage;
