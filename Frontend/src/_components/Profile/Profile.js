import React, { useEffect, useState } from 'react';
import { Container, Typography, Grid, Avatar, Card, CardContent, Box } from '@mui/material';
import { Person } from '@mui/icons-material';
import { fetchListing } from '../../common/common';
import { PageLoader } from '../../common/Loader';

const Profile = () => {

    const [Loading, setLoading] = useState(true)
    const [user, setUser] = useState({})

    useEffect(() => {
        setLoading(true)
        GetProfileDetails()
    }, [])


    const GetProfileDetails = async () => {
        let result = await fetchListing(`/get-profile-details`);
        if (result?.status) {
            setUser(result?.responseData)
        }
        setLoading(false)
    }


    return (
        <Container maxWidth="sm" style={{ marginTop: '20px' }}>
            {Loading ? <PageLoader /> :
                <Card>
                    <Box display="flex" alignItems="center" p={2}>
                        <Avatar style={{ marginRight: '16px', width: '60px', height: '60px' }}>
                            <Person fontSize="large" />
                        </Avatar>
                        <Box>
                            <Typography variant="h5">{user?.name}</Typography>
                            <Typography color="textSecondary">{user?.email}</Typography>
                        </Box>
                    </Box>
                    <CardContent>
                        <Grid container spacing={2}>
                            <Grid item xs={6}>
                                <Typography variant="h6">ID:</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography>{user?.id}</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography variant="h6">Email Verified At:</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography>{user?.email_verified_at ? user?.email_verified_at : 'Not Verified'}</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography variant="h6">Status:</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography>{user?.status === 1 ? 'Active' : 'Inactive'}</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography variant="h6">Stages:</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography>{user?.stages}</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography variant="h6">Created At:</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography>{user?.created_at}</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography variant="h6">Updated At:</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography>{user?.updated_at}</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography variant="h6">Deleted At:</Typography>
                            </Grid>
                            <Grid item xs={6}>
                                <Typography>{user?.deleted_at ? user?.deleted_at : 'Not Deleted'}</Typography>
                            </Grid>
                        </Grid>
                    </CardContent>
                </Card>
            }
        </Container>
    )
}

export default Profile
