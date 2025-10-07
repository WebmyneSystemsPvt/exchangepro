import { yupResolver } from '@hookform/resolvers/yup';
import HowToRegIcon from '@mui/icons-material/HowToReg';
import LoginIcon from '@mui/icons-material/Login';
import {
    Box,
    Button,
    Container,
    CssBaseline,
    Grid,
    IconButton,
    InputAdornment,
    Modal,
    Tab,
    Tabs,
    TextField
} from '@mui/material';
import { createTheme } from '@mui/material/styles';
import React, { useEffect, useState } from 'react';
import { Controller, useForm, useWatch } from 'react-hook-form';
import { useDispatch, useSelector } from 'react-redux';
import { loginValidation, RegistrationValidation } from '../../utils/formValidationSchemas';
// import { WarningModal } from '../../utils/WarningModal';
import { Visibility, VisibilityOff } from '@mui/icons-material';
import GoogleIcon from '@mui/icons-material/Google';
import { addRec } from '../../common/common';
import { loginSuccess, OpenLogin } from '../../store/actions';
import { ErrorToast, SuccessToast } from '../../utils/Toaster';

const theme = createTheme();

function CustomTabPanel(props) {
    const { children, value, index, ...other } = props;

    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`simple-tabpanel-${index}`}
            aria-labelledby={`simple-tab-${index}`}
            {...other}
        >
            {value === index && <Box sx={{ p: 3 }}>{children}</Box>}
        </div>
    );
}


function a11yProps(index) {
    return {
        id: `simple-tab-${index}`,
        'aria-controls': `simple-tabpanel-${index}`,
    };
}




function AuthModal({ isClosed, GoogleLogin }) {

    const dispatch = useDispatch();
    const { OpenLoginModal: ModalProps } = useSelector((state) => state.common);

    // const { token } = useSelector((state) => state.user);

    const [loading, setLoading] = useState(false)
    const [tabValue, setTabValue] = React.useState(0);
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);

    const {
        register,
        handleSubmit,
        formState: { errors },
        clearErrors,
        getValues,
        control,
        reset,
        // reset,
    } = useForm({
        defaultValues: {
            // email: "",
            // password: "",
            email: "",
            password: "",
            confirmPassword: "",
            name: '',
            // email: "testDarpan@gmail.com",
            // password: "Test@123",
            // confirmPassword: "",
            // name: '',
            // role: 2
            // email: "admin@example.com",
            // password: "password"
        },
        resolver: yupResolver(tabValue ? RegistrationValidation : loginValidation),
        shouldUnregister: true,
    });

    const password_Val = useWatch({
        control,
        name: "password",
    });

    useEffect(() => {
        if (ModalProps) {
            if (ModalProps.reg) setTabValue(1)
        }
    }, [ModalProps]);

    const LoginSubmit = async (data) => {
        setLoading(true)
        // delete data.role
        delete data.name
        data.role = 2
        let result = await addRec(`/login`, data);
        if (result?.status) {
            dispatch(OpenLogin({ reg: false, modal: false }))
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
    const RegistrationSubmit = async (data) => {
        setLoading(true)
        delete data.confirmPassword
        data.role = 2
        let result = await addRec(`/register`, data);
        if (result?.status) {
            SuccessToast(result?.message)
            reset()
            setTabValue(0);
        } else {
            ErrorToast(result?.message)
        }
        setLoading(false)
    }


    const handleClickShowPassword = () => setShowPassword(!showPassword);
    const handleClickShowConfirmPassword = () => setShowConfirmPassword(!showConfirmPassword);


    const handleChange = (event, newValue) => {
        setTabValue(newValue);
        clearErrors()
        reset()
    };

    const handleClosed = () => {
        dispatch(OpenLogin({ reg: false, modal: false }))
        setTabValue(0);
        clearErrors()
        reset()
    };


    return (
        <Modal
            open={ModalProps?.modal}
            onClose={handleClosed}
            aria-labelledby="modal-modal-title"
            aria-describedby="modal-modal-description"
        >
            <Container component="main" maxWidth="xs" className='Login_modal'>
                <CssBaseline />

                <Box
                    sx={{
                        // position: 'absolute',
                        // top: tabValue ? '50%' : '50%',
                        // left: '50%',
                        // transform: 'translate(-50%, -50%)',                        
                    }} className='Login_form'
                >
                    <Box sx={{ width: '100%' }}>
                        <Box sx={{ borderBottom: 1, borderColor: 'divider' }}>
                            <Tabs className='tabs_btn' value={tabValue} onChange={handleChange} aria-label="basic tabs example">
                                <Tab icon={<LoginIcon />} label="Login" {...a11yProps(0)} />
                                <Tab icon={<HowToRegIcon />} label="Registration" {...a11yProps(1)} />
                            </Tabs>
                        </Box>
                        <CustomTabPanel value={tabValue} index={0}>
                            <Box
                                sx={{
                                    display: 'flex',
                                    flexDirection: 'column',
                                    alignItems: 'center',
                                }}
                            >

                                {/* <Avatar sx={{ m: 1, bgcolor: 'secondary.main' }}>
                                    <LockOutlinedIcon />
                                </Avatar>
                                <Typography component="h1" variant="h5">
                                    Sign in
                                </Typography> */}


                                <TextField
                                    margin="normal"
                                    fullWidth
                                    id="email"
                                    label="email"
                                    name="email"
                                    autoComplete="email"
                                    {...register('email', {
                                        required: 'Email is required.',
                                    })}
                                    error={!!errors?.email?.message}
                                    helperText={errors?.email?.message}
                                />
                                <TextField
                                    margin="normal"
                                    fullWidth
                                    name="password"
                                    type={showPassword ? 'text' : 'password'}
                                    label="password"
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

                                <Grid container className='sign_btn'>
                                    <Grid item xs={6} gap="2">
                                        <div style={{ padding: "5px" }}>
                                            <Button className='sign_Btn'
                                                onClick={handleSubmit(LoginSubmit)}
                                                type="submit"
                                                fullWidth
                                                variant="contained"
                                                sx={{ mt: 3, mb: 2 }}
                                            >
                                                {loading ? <i className="fa fa-spinner fa-spin"></i> : "Sign In"}
                                            </Button>
                                        </div>
                                    </Grid>
                                    <Grid item xs={6}>
                                        <div style={{ padding: "5px" }}>
                                            <Button className='google_btn'
                                                style={{ padding: "5px" }}
                                                type="submit"
                                                fullWidth
                                                color='warning'
                                                variant="contained"
                                                sx={{ mt: 3, mb: 2 }}
                                                onClick={() => GoogleLogin()}
                                            >
                                                <GoogleIcon />
                                            </Button>
                                        </div>
                                    </Grid>
                                </Grid>
                            </Box>
                        </CustomTabPanel>
                        <CustomTabPanel value={tabValue} index={1}>
                            <Box
                                sx={{
                                    display: 'flex',
                                    flexDirection: 'column',
                                    alignItems: 'center',
                                }}
                            >
                                <Box component="form" onSubmit={handleSubmit(RegistrationSubmit)} sx={{ mt: 1 }}>
                                    {/* <Typography variant="h4" align="center" gutterBottom>
                                        Registration Form
                                    </Typography> */}
                                    <Grid container spacing={2}>
                                        <Grid item xs={12}>
                                            <TextField
                                                fullWidth
                                                label={"Name"}
                                                name="name"
                                                variant="outlined"
                                                {...register('name', {
                                                    required: 'Name is required.',
                                                })}
                                                error={!!errors?.name?.message}
                                                helperText={errors?.name?.message}
                                            />
                                        </Grid>
                                        <Grid item xs={12}>
                                            <TextField
                                                margin="normal"
                                                fullWidth
                                                id="email"
                                                label="Email"
                                                name="Email"
                                                autoComplete="email"
                                                {...register('email', {
                                                    required: 'Email is required.',
                                                })}
                                                error={!!errors?.email?.message}
                                                helperText={errors?.email?.message}
                                            />
                                        </Grid>
                                        <Grid item xs={12}>
                                            <TextField
                                                margin="normal"
                                                fullWidth
                                                name="password"
                                                type={showPassword ? 'text' : 'password'}
                                                label="password"
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
                                        </Grid>

                                        <Grid item xs={12}>
                                            <Controller
                                                name="confirmPassword"
                                                control={control}
                                                defaultValue=""
                                                error={!!errors?.confirmPassword?.message}
                                                helperText={errors?.confirmPassword?.message}
                                                render={({ field }) => (
                                                    <TextField
                                                        {...field}
                                                        margin="normal"
                                                        fullWidth
                                                        type={showConfirmPassword ? 'text' : 'password'}
                                                        label="Confirm Password"
                                                        id="confirmPassword"
                                                        autoComplete="new-password"
                                                        error={!!errors.confirmPassword}
                                                        helperText={errors.confirmPassword?.message}
                                                        InputProps={{
                                                            endAdornment: (
                                                                <InputAdornment position="end">
                                                                    <IconButton
                                                                        aria-label="toggle password visibility"
                                                                        onClick={handleClickShowConfirmPassword}
                                                                        edge="end"
                                                                    >
                                                                        {showConfirmPassword ? <VisibilityOff /> : <Visibility />}
                                                                    </IconButton>
                                                                </InputAdornment>
                                                            ),
                                                        }}
                                                    />
                                                )}
                                            />
                                        </Grid>


                                        {/* <Grid item xs={12}>
                                            <TextField
                                                margin="normal"
                                                fullWidth
                                                id="role"
                                                label="role"
                                                name="role"
                                                autoComplete="role"
                                                disabled
                                                {...register('role')}
                                            />
                                        </Grid> */}
                                        {/* <Grid item xs={12}>
                                            <FormControl fullWidth variant="outlined">
                                                <InputLabel>{"Role"}</InputLabel>
                                                <Select
                                                    name="role"
                                                    {...register('role', {
                                                        required: 'Role is required.',
                                                    })}
                                                    value={role_Val}
                                                    error={!!errors?.role?.message}
                                                >
                                                    <MenuItem value={2}>Borrower</MenuItem>
                                                    <MenuItem value={3}>Seller</MenuItem>
                                                </Select>
                                                {errors.role && <FormHelperText>{errors?.role?.message}</FormHelperText>}
                                            </FormControl>
                                        </Grid> */}
                                        <Grid item xs={12} className='ragister_btn'>
                                            <Button
                                                type="submit"
                                                fullWidth
                                                variant="contained"
                                                color="primary"
                                            >
                                                {loading ? <i className="fa fa-spinner fa-spin"></i> : "Register"}
                                            </Button>
                                        </Grid>
                                    </Grid>
                                </Box>
                            </Box>
                        </CustomTabPanel>
                    </Box>
                </Box>
            </Container>
        </Modal>
    );
}

export default AuthModal;
