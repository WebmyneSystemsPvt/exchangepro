import React from 'react'
import { Box, Typography } from '@mui/material';

const NoData = () => {
    return <Box
        display="flex"
        justifyContent="center"
        alignItems="center"
        height="100vh"
        textAlign="center"
    >
        <Typography variant="h4" component="div">
            No Data Found
        </Typography>
    </Box>
}

export default NoData
