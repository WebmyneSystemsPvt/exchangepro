import { yupResolver } from '@hookform/resolvers/yup';
import LockOutlinedIcon from '@mui/icons-material/LockOutlined';
import {
    Avatar,
    Box,
    Button,
    Container,
    CssBaseline,
    Grid,
    IconButton,
    InputAdornment,
    Link,
    TextField,
    Typography,
} from '@mui/material';
import { createTheme, ThemeProvider } from '@mui/material/styles';
import React, { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { useDispatch, useSelector } from 'react-redux';
import { useHistory } from "react-router-dom";
import { loginValidation } from '../../utils/formValidationSchemas';
// import { WarningModal } from '../../utils/WarningModal';
import { Visibility, VisibilityOff } from '@mui/icons-material';
import { addRec } from '../../common/common';
import { loginSuccess } from '../../store/actions';
import { ErrorToast, SuccessToast } from '../../utils/Toaster';

const theme = createTheme();

function Login() {

    const { token } = useSelector((state) => state.user);

    const dispatch = useDispatch();
    const history = useHistory();
    const [loading, setLoading] = useState(false)

    const {
        register,
        handleSubmit,
        formState: { errors },
        // reset,
    } = useForm({
        defaultValues: {
            email: "",
            role: "seller",
            password: ""
            // email: "seller@gmail.com",
            // role: "seller",
            // password: "Seller@123"
            // email: "admin@example.com",
            // password: "password"
        },
        resolver: yupResolver(loginValidation),
        shouldUnregister: true,
    });

    useEffect(() => {
        if (token) {
            history.push('/home');
        }
    }, [token, history]);

    const Submit = async (data) => {
        setLoading(true)
        let req = { ...data, "role": 3 }
        let result = await addRec(`/login`, req);
        if (result?.status) {
            history.push("/home");
            localStorage.setItem("token", result?.responseData?.access_token);
            SuccessToast(result?.message)
            dispatch(loginSuccess({
                info: result?.responseData?.user,
                profilePic: result?.responseData?.profilePhotoPath,
                isOutOfOffice: result?.responseData?.isCOPSOutOfOffice,
                token: true
            }));
        } else {
            localStorage.removeItem("token");
            ErrorToast(result?.message)
        }
        setLoading(false)
    };

    const [showPassword, setShowPassword] = useState(false);

    const handleClickShowPassword = () => {
        setShowPassword(!showPassword);
    };

    return (
        <ThemeProvider theme={theme}>
            <Container component="main" maxWidth="xs">
                <CssBaseline />
                <Box
                    sx={{
                        marginTop: 8,
                        display: 'flex',
                        flexDirection: 'column',
                        alignItems: 'center',
                    }}
                >
                    <Avatar sx={{ m: 1, bgcolor: 'secondary.main' }}>
                        <LockOutlinedIcon />
                    </Avatar>
                    <Typography component="h1" variant="h5">
                        Sign in
                    </Typography>

                    <Box component="form" onSubmit={handleSubmit(Submit)} sx={{ mt: 1 }}>
                        <TextField
                            margin="normal"
                            fullWidth
                            id="email"
                            label="email"
                            name="email"
                            autoComplete="email"
                            autoFocus
                            {...register('email', {
                                required: 'Email is required.',
                            })}
                            error={!!errors?.email?.message}
                            helperText={errors?.email?.message}
                        />
                        {/* <TextField
                            margin="normal"
                            fullWidth
                            name="password"
                            // label="Password"
                            type="password"
                            id="password"
                            autoComplete="current-password"
                            {...register('password', {
                                required: 'Password is required.',
                            })}
                            error={!!errors?.password?.message}
                            helperText={errors?.password?.message}
                        /> */}
                        <TextField
                            margin="normal"
                            fullWidth
                            name="password"
                            type={showPassword ? 'text' : 'password'}
                            id="password"
                            autoComplete="current-password"
                            {...register('password', {
                                required: 'Password is required.',
                            })}
                            error={!!errors?.password?.message}
                            helperText={errors?.password?.message}
                            InputProps={{
                                endAdornment: (
                                    <InputAdornment position="end">
                                        <IconButton
                                            aria-label="toggle password visibility"
                                            onClick={handleClickShowPassword}
                                            edge="end"
                                        >
                                            {showPassword ? <VisibilityOff /> : <Visibility />}
                                        </IconButton>
                                    </InputAdornment>
                                ),
                            }}
                        />
                        <Button
                            type="submit"
                            fullWidth
                            variant="contained"
                            sx={{ mt: 3, mb: 2 }}
                        >
                            {loading ? <i className="fa fa-spinner fa-spin"></i> : "Sign In"}
                        </Button>
                        <Grid container>
                            {/* <Grid item xs>
                                <Link href="#" variant="body2">
                                    Forgot password?
                                </Link>
                            </Grid> */}
                            <Grid item>
                                <Link onClick={() => history.push('/registration')} variant="body2">
                                    {"Don't have an account? Sign Up"}
                                </Link>
                            </Grid>
                        </Grid>
                    </Box>
                </Box>
            </Container>
        </ThemeProvider>
    );
}

export default Login;
