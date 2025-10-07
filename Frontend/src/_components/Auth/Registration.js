import { yupResolver } from '@hookform/resolvers/yup';
import { Button, Container, FormControl, Grid, InputLabel, MenuItem, Select, TextField, Typography } from '@mui/material';
import React, { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { useHistory } from 'react-router-dom';
import { addRec } from '../../common/common';
import { RegistrationValidation } from './../../utils/formValidationSchemas';
import { useSelector } from 'react-redux';


const Registration = () => {

    const history = useHistory();

    const { token } = useSelector((state) => state.user);

    const [loading, setLoading] = useState(false)

    const {
        register,
        handleSubmit,
        formState: { errors },
        // reset,
    } = useForm({
        defaultValues: {
            name: '',
            email: '',
            password: '',
            role: ''
        },
        resolver: yupResolver(RegistrationValidation),
        shouldUnregister: true,
    });

    const Submit = async (data) => {
        setLoading(true)
        let result = await addRec(`/register`, data);
        if (result?.status) {
            history.push("/login");
        }
        setLoading(false)
    };

    useEffect(() => {
        if (token) {
            history.push('/home');
        }
    }, [token, history]);

    return (
        <Container maxWidth="sm">
            <Typography variant="h4" align="center" gutterBottom>
                Registration Form
            </Typography>
            <form onSubmit={handleSubmit(Submit)}>
                <Grid container spacing={2}>
                    <Grid item xs={12}>
                        <TextField
                            fullWidth
                            label={errors?.name ? errors?.name?.message : "Name"}
                            name="name"
                            variant="outlined"
                            {...register('name', {
                                required: 'Name is required.',
                            })}
                            error={!!errors?.name?.message}
                        />
                    </Grid>
                    <Grid item xs={12}>
                        <TextField
                            fullWidth
                            label={errors?.email ? errors?.email?.message : "Email"}
                            name="email"
                            variant="outlined"
                            type="email"
                            {...register('email', {
                                required: 'Email is required.',
                            })}
                            error={!!errors?.email?.message}
                        />
                    </Grid>
                    <Grid item xs={12}>
                        <TextField
                            fullWidth
                            label={errors?.password ? errors?.password?.message : "Password"}
                            name="password"
                            variant="outlined"
                            type="password"
                            {...register('password', {
                                required: 'Password is required.',
                            })}
                            error={!!errors?.password?.message}
                        />
                    </Grid>
                    <Grid item xs={12}>
                        <FormControl fullWidth variant="outlined">
                            <InputLabel>{errors?.role ? errors?.role?.message : "Role"}</InputLabel>
                            <Select
                                name="role"
                                {...register('role', {
                                    required: 'Role is required.',
                                })}
                                error={!!errors?.role?.message}
                            >
                                <MenuItem value="borrower">Borrower</MenuItem>
                                <MenuItem value="lender">Lender</MenuItem>
                            </Select>
                            {/* {errors.role && <FormHelperText>{errors.role}</FormHelperText>} */}
                        </FormControl>
                    </Grid>
                    <Grid item xs={6}>
                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            color="primary"
                        >
                            {loading ? <i className="fa fa-spinner fa-spin"></i> : "Register"}
                        </Button>
                    </Grid>
                    <Grid item xs={6}>
                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            color="primary"
                            onClick={() => history.push('/login')}
                        >
                            Login
                        </Button>
                    </Grid>
                </Grid>
            </form>
        </Container>
    );
};

export default Registration;
