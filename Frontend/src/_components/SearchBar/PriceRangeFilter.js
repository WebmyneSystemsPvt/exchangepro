import { Box, Input, Slider, Typography } from '@mui/material';
import { styled } from '@mui/material/styles';
import React, { useEffect, useState, useCallback } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { debounce } from 'lodash';
import { SearchProperties } from '../../store/actions/common';

const CustomSlider = styled(Slider)({
    color: 'white',
    '& .MuiSlider-thumb': {
        color: 'black',
    },
    '& .MuiSlider-track': {
        color: 'black',
    },
    '& .MuiSlider-rail': {
        color: 'white',
    },
});

const PriceRangeFilter = ({ PriceRange, Data }) => {

    const dispatch = useDispatch();
    const { ResetSearch } = useSelector((state) => state.common);

    const [value, setValue] = useState([0, 50000]);

    useEffect(() => {
        if (Data?.price_max > 0) {
            setValue([Data?.price_min, Data?.price_max]);
        } else {
            setValue([0, 50000]);
        }
    }, [Data]);

    const handleChange = (event, newValue) => {
        setValue(newValue);
    };

    const handleChangeCommitted = (event, newValue) => {
        PriceRange(newValue);
    };

    const debouncedPriceRange = useCallback(
        debounce((newValue) => {
            PriceRange(newValue);
        }, 500),
        []
    );

    const handleInputChange = (event, index) => {
        const newValue = [...value];
        newValue[index] = event.target.value === '' ? '' : Number(event.target.value);
        setValue(newValue);
        debouncedPriceRange(newValue);
    };

    const handleBlur = () => {
        if (value[0] < 0) {
            setValue([0, value[1]]);
        } else if (value[1] > 50000) {
            setValue([value[0], 50000]);
        } else if (value[0] > value[1]) {
            setValue([value[1], value[0]]);
        }
    };

    useEffect(() => {
        if (ResetSearch) {
            dispatch(SearchProperties(false));
            setValue([0, 50000]);
        }
    }, [ResetSearch]);

    return (
        <Box sx={{ padding: '20px', borderRadius: '8px', boxShadow: 1, backgroundColor: 'white' }}>
            <CustomSlider
                value={value}
                onChange={handleChange}
                onChangeCommitted={handleChangeCommitted}
                valueLabelDisplay="auto"
                min={0}
                max={50000}
                step={100}
                sx={{ marginBottom: 2 }}
            />
            <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <Box sx={{ display: 'flex', alignItems: 'center' }}>
                    <Typography variant="subtitle1">₹</Typography>
                    <Input
                        value={value[0]}
                        size="small"
                        onChange={(e) => handleInputChange(e, 0)}
                        onBlur={handleBlur}
                        inputProps={{
                            step: 100,
                            min: 0,
                            max: 50000,
                            type: 'number',
                            'aria-labelledby': 'input-slider',
                        }}
                        sx={{ marginLeft: 1, width: 80 }}
                    />
                </Box>
                <Typography variant="body1"> - </Typography>
                <Box sx={{ display: 'flex', alignItems: 'center' }}>
                    <Typography variant="subtitle1">₹</Typography>
                    <Input
                        value={value[1]}
                        size="small"
                        onChange={(e) => handleInputChange(e, 1)}
                        onBlur={handleBlur}
                        inputProps={{
                            step: 100,
                            min: 0,
                            max: 50000,
                            type: 'number',
                            'aria-labelledby': 'input-slider',
                        }}
                        sx={{ marginLeft: 1, width: 80 }}
                    />
                </Box>
            </Box>
        </Box>
    );
};

export default PriceRangeFilter;
