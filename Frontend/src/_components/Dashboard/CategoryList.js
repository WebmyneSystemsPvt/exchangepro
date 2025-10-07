import React, { useEffect, useState } from 'react'
import {
    Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Paper, Avatar,
    IconButton, Collapse, Box, Typography
} from '@mui/material';
import { KeyboardArrowDown, KeyboardArrowUp } from '@mui/icons-material';
import { PageLoader } from '../../common/Loader';
import { fetchListing } from '../../common/common';

const CategoryList = () => {

    const [Loading, setLoading] = useState(true)
    const [CategoryData, setCategoryData] = useState([])
    const [open, setOpen] = useState({});


    const handleClick = (id) => {
        setOpen((prevOpen) => ({ ...prevOpen, [id]: !prevOpen[id] }));
    };


    useEffect(() => {
        setLoading(true)
        GetCategoryDetails()
    }, [])


    const GetCategoryDetails = async () => {
        let result = await fetchListing(`/categories`);
        if (result?.status) {
            setCategoryData(result?.responseData)
        }
        setLoading(false)
    }


    return (
        <TableContainer component={Paper} sx={{ marginTop: '20px' }}>
            {Loading ? <PageLoader /> :
                <Table>
                    <TableHead>
                        <TableRow sx={{ backgroundColor: '#f5f5f5' }}>
                            <TableCell />
                            <TableCell>ID</TableCell>
                            <TableCell>Name</TableCell>
                            <TableCell>Status</TableCell>
                            <TableCell>Created At</TableCell>
                            <TableCell>Updated At</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {CategoryData.map((category) => (
                            <React.Fragment key={category.id}>
                                <TableRow>
                                    <TableCell>
                                        <IconButton size="small" onClick={() => handleClick(category.id)}>
                                            {open[category.id] ? <KeyboardArrowUp /> : <KeyboardArrowDown />}
                                        </IconButton>
                                    </TableCell>
                                    <TableCell>{category.id}</TableCell>
                                    <TableCell>
                                        <Box display="flex" alignItems="center">
                                            <Avatar sx={{ bgcolor: '#1976d2', marginRight: '10px' }}>{category.name[0]}</Avatar>
                                            {category.name}
                                        </Box>
                                    </TableCell>
                                    <TableCell>{category.status === 0 ? 'Inactive' : 'Active'}</TableCell>
                                    <TableCell>{category.created_at}</TableCell>
                                    <TableCell>{category.updated_at}</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell style={{ paddingBottom: 0, paddingTop: 0 }} colSpan={6}>
                                        <Collapse in={open[category.id]} timeout="auto" unmountOnExit>
                                            <Box margin={1}>
                                                <Typography variant="h6" gutterBottom component="div">
                                                    Subcategories
                                                </Typography>
                                                <Table size="small" aria-label="subcategories">
                                                    <TableHead>
                                                        <TableRow sx={{ backgroundColor: '#e0e0e0' }}>
                                                            <TableCell></TableCell>
                                                            <TableCell></TableCell>
                                                            <TableCell>ID</TableCell>
                                                            <TableCell>Name</TableCell>
                                                            <TableCell>Status</TableCell>
                                                            <TableCell>Created At</TableCell>
                                                            <TableCell>Updated At</TableCell>
                                                        </TableRow>
                                                    </TableHead>
                                                    <TableBody>
                                                        {category.sub_categories.map((subCategory) => (
                                                            <TableRow key={subCategory.id}>
                                                                <TableCell></TableCell>
                                                                <TableCell></TableCell>
                                                                <TableCell>{subCategory.id}</TableCell>
                                                                <TableCell>
                                                                    <Box display="flex" alignItems="center">
                                                                        <Avatar sx={{ bgcolor: '#1976d2', marginRight: '10px' }}>{subCategory.name[0]}</Avatar>
                                                                        {subCategory.name}
                                                                    </Box>
                                                                </TableCell>
                                                                <TableCell>{subCategory.status === 0 ? 'Inactive' : 'Active'}</TableCell>
                                                                <TableCell>{subCategory.created_at}</TableCell>
                                                                <TableCell>{subCategory.updated_at}</TableCell>
                                                            </TableRow>
                                                        ))}
                                                    </TableBody>
                                                </Table>
                                            </Box>
                                        </Collapse>
                                    </TableCell>
                                </TableRow>
                            </React.Fragment>
                        ))}
                    </TableBody>
                </Table>
            }
        </TableContainer>
    )
}

export default CategoryList
