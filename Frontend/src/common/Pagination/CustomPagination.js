import Pagination from '@mui/material/Pagination';
import Stack from '@mui/material/Stack';
import React from 'react';
import NoData from '../NoData';

const CustomPagination = ({ pageNumber, pageSize, totalItems, totalPages, onPageChange, onPageSizeChange }) => {
  const handlePageChange = (event, value) => {
    onPageChange(value);
  };

  const handlePageSizeChange = (event) => {
    onPageSizeChange(event.target.value);
  };

  return totalPages > 1 ? (
    <Stack spacing={2} direction="row" justifyContent="center" alignItems="center">
      {/* <FormControl variant="outlined" size="small">
        <InputLabel id="page-size-label">Items per page</InputLabel>
        <Select
          labelId="page-size-label"
          value={pageSize}
          onChange={handlePageSizeChange}
          label="Items per page"
        >
          {[5, 10, 20, 50].map((size) => (
            <MenuItem key={size} value={size}>
              {size}
            </MenuItem>
          ))}
        </Select>
      </FormControl> */}
      <Pagination
        count={totalPages}
        page={pageNumber}
        onChange={handlePageChange}
        variant="outlined"
        shape="rounded"
        size="large"
      />
      {/* <span>{`Showing ${pageSize * (pageNumber - 1) + 1}-${Math.min(pageSize * pageNumber, totalItems)} of ${totalItems} items`}</span> */}
    </Stack>
  ) : totalPages === 0 && <NoData />;
};

export default CustomPagination;
