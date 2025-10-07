import React from 'react';
import { CircularProgress, Box } from '@mui/material';


export const Loader = () => {
    return (
        <Box
            sx={{
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                backgroundColor: 'rgba(255, 255, 255, 0.8)',
            }}
        >
            <CircularProgress />
        </Box>
    )
}

export const ButtonLoader = () => {
    return (
        <i class="fas fa-spinner fa-spin"></i>
    )
}

export const PageLoader = () => {
    return (
        <Box
            sx={{
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                height: '100vh',
                backgroundColor: 'rgba(255, 255, 255, 0.8)',
            }}
        >
            <CircularProgress />
        </Box>
    );
}
