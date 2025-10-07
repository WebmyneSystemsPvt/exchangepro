import { CircularProgress, TableCell, TableRow } from "@mui/material";
import { Skeleton } from "@mui/material";
import { useHistory } from "react-router-dom";

export const LoadingSkeletons = () => (
  <div className="flex-grow-1">
    <Skeleton />
    <Skeleton animation="wave" />
    <Skeleton animation={false} />
    <Skeleton animation="wave" />
    <Skeleton />
  </div>
);

export const FallbackLoadingSpinner = ({ title }) => {
  return (
      <div className="d-flex align-items-center w-100">
        <CircularProgress color="primary" className="mr-4 ml-2" />
        {/* <LoadingSkeletons /> */}
      </div>
  );
};

export const ContentLoadingSpinner = () => {
  return (
    <>
      <div className="d-flex align-items-center w-100">
        <CircularProgress color="primary" className="mr-4 ml-2" />
        <LoadingSkeletons />
      </div>
    </>
  );
};

export const OverlayLoadingSpinner = () => {
  return (
    <div
      style={{ minHeight: "100vh" }}
      className="w-100 h-100 d-flex justify-content-center align-items-center"
    >
      <CircularProgress color="secondary" size={75} />
    </div>
  );
};

export const DashboardSpinner = () => {
  return (
    <>
      <div className="d-flex align-items-center p-4">
        <CircularProgress color="primary" className="mr-4 ml-2" />
        <div className="flex-grow-1">
          <Skeleton />
          <Skeleton animation="wave" />
          <Skeleton animation={false} />
          <Skeleton animation="wave" />
          <Skeleton />
        </div>
      </div>
    </>
  );
};

export const NoRecordsFoundDiv = () => {
  return (
    <div
      style={{
        display: "flex",
        minHeight: "30vh",
        justifyContent: "center",
        alignItems: "center",
        flexDirection: "column",
      }}
    >
      <h6>No Records Found...</h6>
      {/* <button className="btn btn-primary " onClick={() => history.goBack()}>
        <i className="fa fa-arrow-left" /> Go Back
      </button> */}
    </div>
  );
};

export const TimerNoRecordsFound = () => {
  return (
    <TableRow
      style={{
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
        // background: "#c8ddef",
      }}
    >
      <TableCell
        style={{
          fontSize: "1.03rem",
          padding: "3% 0",
          fontWeight: "bold",
          textAlign: "center",
        }}
        colSpan={14}
      >
        <i className="fa fa-blink fa-times-circle text-danger"></i> No Events
        Found...
      </TableCell>
    </TableRow>
  );
};

export const NoRecordsFoundRow = () => {
  return (
    <TableRow>
      <TableCell
        style={{
          fontSize: "1.03rem",
          padding: "3% 0",
          fontWeight: "bold",
          textAlign: "center",
        }}
        colSpan={14}
      >
        <i className="fa fa-blink fa-times-circle text-danger"></i> No Records
        Found...
      </TableCell>
    </TableRow>
  );
};

export const LoadingHeaderRow = () => {
  return (
    <TableRow>
      <TableCell></TableCell>
      <TableCell colSpan={2}>
        <i className="fas fa-spinner fa-spin"></i>
      </TableCell>
      <TableCell colSpan={2}>
        <i className="fas fa-spinner fa-spin"></i>
      </TableCell>
      <TableCell colSpan={2}>
        <i className="fas fa-spinner fa-spin"></i>
      </TableCell>
    </TableRow>
  );
};

export const ErrorFetchingRecordsDiv = () => {
  const history = useHistory();
  return (
    <div
      style={{
        display: "flex",
        minHeight: "30vh",
        justifyContent: "center",
        alignItems: "center",
        flexDirection: "column",
      }}
    >
      <h6>Oopps! Error Occured While Fetching Records...</h6>
      <button className="btn btn-primary " onClick={() => history.push("/")}>
        <i className="fa fa-arrow-left" /> Back to dashboard
      </button>
    </div>
  );
};

export const loadingAutoCompleteSpinner = (
  <>
    <i className="fa fa-spinner fa-spin"></i> Loading...
  </>
);
