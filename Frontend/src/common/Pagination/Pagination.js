import React from 'react';
import { Pagination, PaginationItem } from '@mui/material';
import { styled } from '@mui/system';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import ArrowForwardIcon from '@mui/icons-material/ArrowForward';

const CustomPagination = styled(Pagination)({
    '& .MuiPaginationItem-root': {
        color: '#87431D',
    },
    '& .MuiPaginationItem-root.Mui-selected': {
        backgroundColor: '#F4CE3B',
        color: '#000',
    },
    '& .MuiPaginationItem-root:hover': {
        backgroundColor: 'rgba(244, 206, 59, 0.5)',
    },
});

const PaginationComponent = ({ count, page, onChange }) => {
    return (
        <CustomPagination
            count={count}
            page={page}
            onChange={onChange}
            renderItem={(item) => (
                <PaginationItem
                    components={{ previous: ArrowBackIcon, next: ArrowForwardIcon }}
                    {...item}
                />
            )}
        />
    );
};

export default PaginationComponent;
