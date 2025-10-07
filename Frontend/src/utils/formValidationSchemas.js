import axios from "axios";
import * as yup from "yup";
import axiosConfig from "./axiosConfig";
import { validateEmail, currentYear } from "./utils";

/* 
  ------------------
  -> isUserName USERNAME
  ------------------
*/

yup.addMethod(yup.string, "username", function (errorMessage) {
  return this.test("username", errorMessage, async function (value) {
    const { path, createError } = this;

    let schema = yup
      .string()
      .matches(
        new RegExp(`^(?=[a-zA-Z0-9._]{5,20}$)(?!.*[_.]{2})[^_.].*[^_.]$`)
      );

    try {
      const isValid = await schema.isValid(value || "");

      if (!isValid) {
        return createError({
          path,
          message: "Special Characters and White Space/s are not allowed!",
        });
      }
      return value;
    } catch (err) {
      console.log(err);
    }
  });
});

/* 
  ------------------
  -> CHECK UNIQUE USERNAME
  ------------------
*/

let uniqueUsernameCancelToken;

yup.addMethod(yup.string, "uniqueUsername", function (errorMessage) {
  return this.test("unique-username", errorMessage, async function (value) {
    const { path, createError } = this;

    try {
      if (!value) {
        return createError({ path, message: "Please Enter Username!" });
      }
      if ((value || "").length < 5) {
        return createError({
          path,
          message: "Username Must be greater than 4 characters!",
        });
      }
      if (uniqueUsernameCancelToken) {
        uniqueUsernameCancelToken.cancel();
      }
      uniqueUsernameCancelToken = axios.CancelToken.source();

      let response = await axiosConfig.post(
        "/checkUniqueUsername",
        {
          username: value,
        },
        { cancelToken: uniqueUsernameCancelToken.token }
      );

      if (response?.data?.status === 1) {
        return value;
      } else {
        return createError({ path, message: errorMessage });
      }
    } catch (err) {
      if (err.constructor.name !== "Cancel")
        return createError({
          path,
          message: "Error Occurred while Checking Email.",
        });
    }
  });
});

/* 
  ------------------
  -> CHECK UNIQUE EMAIL
  ------------------
*/

let uniqueEmailCancelToken;

let uniqueEmailCache = [];

const addToUniqueEmailCache = (query, isUnique) => {
  uniqueEmailCache.push({ query, isUnique });
  // Remove Item From Cache After 1 minute
  setTimeout(() => {
    uniqueEmailCache.splice(uniqueEmailCache.length - 1, 1);
  }, 60000);
};
yup.addMethod(yup.string, "uniqueEmail", function (errorMessage) {
  return this.test("unique-email", errorMessage, async function (value) {
    const { path, createError } = this;
    if (!value || !validateEmail(value)) {
      return createError({ path, message: "Please Enter a valid Email!" });
    }
    let fromCache = uniqueEmailCache.find((i) => i.query === value);
    if (fromCache) {
      if (fromCache.isUnique) return value;
      return createError({ path, message: errorMessage });
    }

    try {
      if (uniqueEmailCancelToken) {
        uniqueEmailCancelToken.cancel();
      }
      uniqueEmailCancelToken = axios.CancelToken.source();

      let response = await axiosConfig.post(
        "/checkUniqueEmail",
        {
          email: value,
        },
        { cancelToken: uniqueEmailCancelToken.token }
      );

      if (response?.data?.status === 1) {
        addToUniqueEmailCache(value, true);

        return value;
      } else {
        addToUniqueEmailCache(value, false);
        return createError({ path, message: errorMessage });
      }
    } catch (err) {
      if (err.constructor.name !== "Cancel")
        return createError({
          path,
          message: "Error Occurred while Checking Email.",
        });
    }
  });
});

/* 
  ------------------
  -> Login
  ------------------
*/
export const loginValidation = yup.object().shape({
  email: yup.string().required("Email/Username is Required!"),
  password: yup
    .string()
    .min(6, "Password Must be of minimum 6 Characters!")
    .required("Password is Required!"),
});

/* 
  ------------------
  -> Registration
  ------------------
*/

const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;

export const RegistrationValidation = yup.object().shape({
  name: yup.string().required("Name is Required !"),
  email: yup.string().required("Email is Required !"),
  password: yup
    .string()
    .matches(passwordRegex, 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character.')
    .required('Password is Required !'),
  confirmPassword: yup
    .string()
    .oneOf([yup.ref('password'), null], 'Passwords must match')
    .required('Confirm Password is Required !'),
  // role: yup.string().required("Role is Required !"),
});

/* 
  ------------------
  -> Admin Users
  ------------------
*/
export const AddItemValidation = yup.object().shape({
  listing_type: yup.string().required('Name is required'),
  categories_id: yup.number().required('Categories ID is required'),
  sub_categories_id: yup.number().required('Sub Categories ID is required'),
  description: yup.string().required('Description is required'),
  location: yup.string().required('Location is required'),
  exception_details: yup.string().required('Exception Details is required'),
  rate: yup.string().required('Rate is required'),
  rented_max_allow_days: yup.number().required('Rented Max Allow Days is required'),
  blocked_days: yup.array().min(1, 'At least one Blocked Days is required').required('Blocked Days is required'),
  item_id: yup.array().of(yup.number()).min(1, 'At least one Item ID is required').required('Item IDs are required'),
  country: yup.string().required('Country is required'),
  state: yup.string().required('State is required'),
  city: yup.string().required('City is required'),
  pincode: yup.string().required('Pincode is required'),
  landmark: yup.string().required('Landmark is required'),
  latitude: yup.string().required('Latitude is required'),
  longitude: yup.string().required('Longitude is required'),
  tags: yup.array().min(1, 'At least one Tags is required').required('Tags are required'),
  // storage_photos: yup.array().min(1, 'At least one Storage Photo is required').required('Storage Photos are required'),
});

export const step1ValidationSchema = yup.object().shape({
  listing_type: yup.string().required('Name is required'),
  categories_id: yup.number().required('Categories ID is required'),
  sub_categories_id: yup.number().required('Sub Categories ID is required'),
  description: yup.string().required('Description is required'),
});

export const step2ValidationSchema = yup.object().shape({
  location: yup.string().required('Location is required'),
  exception_details: yup.string().required('Exception Details is required'),
  rate: yup.string().required('Rate is required'),
  rented_max_allow_days: yup.number().required('Rented Max Allow Days is required'),
  blocked_days: yup.array().min(1, 'At least one Blocked Days is required').required('Blocked Days is required'),
  item_id: yup.array().of(yup.number()).min(1, 'At least one Item ID is required').required('Item IDs are required'),
  country: yup.string().required('Country is required'),
  state: yup.string().required('State is required'),
  city: yup.string().required('City is required'),
  pincode: yup.string().required('Pincode is required'),
  landmark: yup.string().required('Landmark is required'),
  latitude: yup.string().required('Latitude is required'),
  longitude: yup.string().required('Longitude is required'),
  tags: yup.array().min(1, 'At least one Tags is required').required('Tags are required'),
});

export const step3ValidationSchema = yup.object().shape({
  
});

export const AddReviewValidationSchema = yup.object().shape({
  title: yup.string().required('Rating is required'),
  description: yup.string().required('Description is required'),
});


export const updateAdminUserSchema = yup.object().shape({
  first_name: yup.string().required("First Name Cannot be Empty"),
  last_name: yup.string().required("Last Name Cannot be Empty"),
});

/* 
  ------------------
  -> Modules
  ------------------
*/
export const moduleSchema = yup.object().shape({
  module_name: yup.string().required("Module Name is Required!"),
  status: yup.string().required("Please Select Status"),
  // module_url: yup.string().required("Module URL is Required"),
});

export const servicesSchema = yup.object().shape({
  service_number: yup.string().required("Service Number is Required!"),
  service_name: yup.string().required("Service Name is Required!"),
  // module_url: yup.string().required("Module URL is Required"),
});

/* 
  ------------------
  -> Role
  ------------------
*/
export const addRole = yup.object().shape({
  name: yup.string().required("Role Name is Required!"),
});

export const editRoleName = yup.object().shape({
  name: yup.string().required("Role Name Cannot be Empty"),
});

/* 
  ------------------
  -> Unit
  ------------------
*/
export const addUnit = yup.object().shape({
  unit_name: yup.string().required("Unit Name is Required!"),
});

export const editUnitName = yup.object().shape({
  unit_name: yup.string().required("Unit Name Cannot be Empty"),
});

/* 
  ------------------
  -> Employee Type
  ------------------
*/
export const addEmployeeType = yup.object().shape({
  emp_type_name: yup.string().required("Type Name is Required!"),
});

export const editEmployeeType = yup.object().shape({
  emp_type_name: yup.string().required("Type Name Cannot be Empty"),
  emp_type_status: yup.string().required("Please Select status"),
});

/* 
  ------------------
  - Rate Level
  ------------------
*/
export const addRateLevel = yup.object().shape({
  rate_level_name: yup.string().required("Level Name is Required!"),
});

export const editRateLevelName = yup.object().shape({
  rate_level_name: yup.string().required("Level Name Cannot be Empty"),
});

/* 
  ------------------
  - Client Add
  ------------------
*/

export const clientInitialSchema = {
  client_type: "Personal",
  personal_details: {
    // dependant: [{ name: "", isAbove18: "no" }],
    hst_year_end: "",
    hst_frequency: "None",
  },
  corporate_details: {
    year_end: "1/1",
    hst_year_end: "",
    submittingRef: false,
    hst_frequency: "None",
    contactDetails: {
      individual_filing: "no",
      allow_login: "no",
      personal_details: {
        // dependant: [{ name: "", isAbove18: "no" }],
        servicesYear: currentYear,
      },
    },
  },
};

export const clientValidationKeys = {
  // 0: ["client_ids", "company_name", "client_type"],
  0: ["client_type"],
  1: {
    Personal: [
      "personal_details.first_name",
      "personal_details.last_name",
      "personal_details.dob",
      "personal_details.marital_status",
      "personal_details.dependant",
      "personal_details.sin",
      "personal_details.street_address",
      "personal_details.city",
      "personal_details.province",
      "personal_details.country",
      "personal_details.postal_code",
      "personal_details.phone_number",
      "personal_details.email",
      "personal_details.username",
      "personal_details.joining_date",
      "personal_details.alternate_phone_number",
      "personal_details.hst_year_end",
      "personal_details.hst_frequency",
      // "personal_details.status",
    ],
    Corporate: [
      "company_name",
      "corporate_details.legal_name",
      "corporate_details.operating_name",
      "corporate_details.primary_contact",
      "corporate_details.street_address",
      "corporate_details.city",
      "corporate_details.province",
      "corporate_details.country",
      "corporate_details.postal_code",
      "corporate_details.phone_number",
      "corporate_details.alternate_phone_number",
      "corporate_details.email",
      "corporate_details.username",
      "corporate_details.business_number",
      "corporate_details.doi",
      "corporate_details.year_end",
      "corporate_details.province_of_incorporation",
      "corporate_details.hst_year_end",
      "corporate_details.hst_frequency",
    ],
  },
  2: {
    Personal: [],
    Corporate: ["corporate_details.client_job_codes"],
  },
  3: {
    Corporate: [],
    Personal: ["personal_details.services_year"],
  },
};
export const clientPersonalDetailsSchema = {
  first_name: yup.string().required("First Name is Required!"),
  last_name: yup.string().required("Last Name is Required!"),
  dob: yup.string().required("Date of Birth is Required!"),
  dependant: yup.array().of(
    yup.object().shape({
      name: yup
        .string()
        .min(3, "Dependant Name must be atleast 3 Characters.")
        .required("Please Enter Dependant Name"),
      isAbove18: yup.string().required("Is Dependant Above 18 ?"),
    })
  ),
  // .min(1, "Please Add Atleast One dependant!")
  sin: yup.string().required("SIN is Required!"),
  street_address: yup.string().required("Street Address is Required!"),
  city: yup.string().required("City is Required!"),
  province: yup.string().required("Province is Required!"),
  country: yup.number().required("Country is Required!"),
  postal_code: yup.string().required("Postal Code is Required!"),
  phone_number: yup.string().required("Phone Number is Required!"),
  validateEmail: yup.boolean().default(true),
  email: yup.string().when("validateEmail", {
    is: true,
    then: yup
      .string()
      .email("Enter a Valid Email Address!")
      .required("Email is Required!"),
    // .uniqueEmail("The Email is already in use."),
  }),
  username: yup.string().when("validateEmail", {
    is: true,
    then: yup
      .string()
      .required("username is Required!")
      .transform((value) => (value || "").trim().toLowerCase())
      .username()
      .uniqueUsername("The Username is already in use."),
  }),
  joining_date: yup.string().required("Joining Date is Required!"),
  // status: yup.string().required("Status is Required!"),
};

export const clientContactDetailsSchema = {
  first_name: yup.string().required("First Name is Required!"),
  last_name: yup.string().required("Last Name is Required!"),
  phone_number: yup.string().required("Phone Number is Required!"),
  validateEmail: yup.boolean().default(true),
  email: yup
    .string()
    .email("Enter a Valid Email Address!")
    .required("Email is Required!"),
  username: yup
    .string()
    .when(
      [
        "existing_individual_filing",
        "allow_login",
        "individual_filing",
        "existing_allow_login",
      ],
      {
        is: (
          existing_individual_filing,
          allow_login,
          individual_filing,
          existing_allow_login
        ) =>
          (allow_login === "yes" || individual_filing === "yes") &&
          (!existing_individual_filing ||
            existing_individual_filing === "no") &&
          (!existing_allow_login || existing_allow_login === "no"),
        then: yup
          .string()
          .transform((value) => (value || "").trim().toLowerCase())
          .username()

          .required("username is Required!")
          .uniqueUsername("The Username is already in use."),
      }
    ),
  individual_filing: yup.string().required("Individual Filing is Required!"),
  allow_login: yup.string().when("individual_filing", {
    is: "no",
    then: yup.string().required("Allow Login Is Required!"),
  }),
  personal_details: yup.object().when("individual_filing", {
    is: "yes",
    then: yup.object({
      dob: yup.string().required("Date of Birth is Required!"),
      dependant: yup.array().of(
        yup.object().shape({
          name: yup
            .string()
            .min(3, "Dependant Name must be atleast 3 Characters.")
            .required("Please Enter Dependant Name"),
          isAbove18: yup.string().required("Is Dependant Above 18 ?"),
        })
      ),
      // .min(1, "Please Add Atleast One dependant!")
      sin: yup.string().required("SIN is Required!"),
      street_address: yup.string().required("Street Address is Required!"),
      city: yup.string().required("City is Required!"),
      province: yup.string().required("Province is Required!"),
      country: yup.string().required("Country is Required!"),
      postal_code: yup.string().required("Postal Code is Required!"),
      // phone_number: yup.string().required("Phone Number is Required!"),
      // email: yup.string().required("Email is Required!"),
      joining_date: yup.string().required("Joining Date is Required!"),
      // status: yup.string().required("Status is Required!"),
      services_year: yup.string().required("Year is Required!"),
    }),
  }),
};

// Dependant

export const PersonalClientDependantSchema = yup.object().shape({
  name: yup.string().required("Name is Required!"),
  individual_filing: yup.string().required("Individual Filing is Required!"),
  isAbove18: yup.string().required("IsAbove18 is Required!"),
  personal_details: yup.object().when("individual_filing", {
    is: "yes",
    then: yup.object({
      // first_name: yup.string().required("First Name is Required!"),
      last_name: yup.string().required("Last Name is Required!"),
      phone_number: yup.string().required("Phone Number is Required!"),
      validateEmail: yup.boolean().default(true),
      email: yup
        .string()
        .email("Enter a Valid Email Address!")
        .required("Email is Required!"),

      username: yup.string().when("existing_individual_filing", {
        is: (existing) => !existing || existing === "no",
        then: yup
          .string()
          .transform((value) => (value || "").trim().toLowerCase())
          .username()
          .required("username is Required!")
          .uniqueUsername("The Username is already in use."),
      }),
      dob: yup.string().required("Date of Birth is Required!"),
      sin: yup.string().required("SIN is Required!"),
      street_address: yup.string().required("Street Address is Required!"),
      city: yup.string().required("City is Required!"),
      province: yup.string().required("Province is Required!"),
      country: yup.string().required("Country is Required!"),
      postal_code: yup.string().required("Postal Code is Required!"),

      joining_date: yup.string().required("Joining Date is Required!"),
      services_year: yup.string().required("Service Year is Required!"),
    }),
  }),
});

export const clientCorporateDetailsSchema = {
  submittingRef: yup.boolean(),
  legal_name: yup.string().required("Legal Name is Required!"),
  operating_name: yup.string().required("Operating Name is Required!"),
  primary_contact: yup.string().required("Primary Contact is Required!"),
  street_address: yup.string().required("Street Address is Required!"),
  city: yup.string().required("City is Required!"),
  province: yup.string().required("Province is Required!"),
  country: yup.string().required("Country is Required!"),
  postal_code: yup.string().required("Postal Code is Required!"),
  phone_number: yup.string().required("Phone Number is Required!"),
  validateEmail: yup.boolean().default(true),
  email: yup.string().when("validateEmail", {
    is: true,
    then: yup
      .string()
      .email("Enter a Valid Email Address!")
      .required("Email is Required!"),
    // .uniqueEmail("The Email is already in use."),
  }),
  username: yup.string().when("validateEmail", {
    is: true,
    then: yup
      .string()
      .transform((value) => (value || "").trim().toLowerCase())
      .username()
      .required("username is Required!")
      .uniqueUsername("The Username is already in use."),
  }),

  business_number: yup.string().required("Business Number is Required!"),
  // doi: yup.string().required("DOI is Required!"),
  year_end: yup.string().required("Year End is Required!"),
  province_of_incorporation: yup
    .string()
    .required("Province of Incorporation is Required!"),
};

export const clientAddSchema = yup.object().shape({
  // client_ids: yup.string().required("Client ID is Required"),
  client_type: yup
    .string("Please Select Client Type")
    .required("Client Type is Required"),
  personal_details: yup.object().when("client_type", {
    is: "Personal",
    then: yup.object({
      ...clientPersonalDetailsSchema,
    }),
  }),
  company_name: yup.string().when("client_type", {
    is: "Corporate",
    then: yup.string().required("Company Name is Required"),
  }),

  corporate_details: yup.object().when("client_type", {
    is: "Corporate",
    then: yup.object({
      ...clientCorporateDetailsSchema,
      client_job_codes: yup
        .array()
        .min(1, "Atleast One Job Code Must be Selected"),

      // CONTACT DETAILS
      contactDetails: yup.object().when("submittingRef", {
        is: false,
        then: yup.object({ ...clientContactDetailsSchema }),
      }),
    }),
  }),
});

export const companyValidation = yup.object().shape({
  // company_name: yup.string().required("Company Name Cannot Be Empty"),
});
/*
  ------------------
  - Job Code
  ------------------
*/
export const jobcodeValidation = yup.object().shape({
  job_code: yup.string().required("Job Code is Required!"),
  job_code_name: yup.string().required("Job Code Name is Required!"),
  job_code_description: yup
    .string()
    .required("Job Code description is Required!"),
  job_code_type: yup.string().required("Please Select Job Code Type"),
  hierachy: yup.string().required("Please Select Hierachy"),
  rate_level_id: yup.string().required("Please Select Rate Level"),
  unit_id: yup.string().required("Please Select an Unit"),
});

/* 
  ------------------
  - Employee
  ------------------
*/

export const employeeValidation = yup.object().shape({
  employee_id: yup.string().required("Please enter Employee Id"),
  username: yup.string().when("emp_id", {
    is: (value) => !value,
    then: yup
      .string()
      .transform((value) => (value || "").trim().toLowerCase())
      .username()
      .required("Username is Required!")
      .uniqueUsername("Username Must be unique"),
  }),
  validateEmail: yup.boolean().default(true),
  email: yup
    .string()
    .email("Enter a Valid Email Address!")
    .required("Email is Required!"),
  // email: yup.string().when("validateEmail", {
  //   is: true,
  //   then: yup
  //     .string()
  //     .email("Enter a Valid Email Address!")
  //     .required("Email is Required!")
  //     .uniqueEmail("The Email is already in use."),
  // }),
  employee_type: yup.string().required("Please Select Employee Type"),
  first_name: yup.string().required("First Name is Required!"),
  last_name: yup.string().required("Last Name is Required!"),
  dob: yup.string().required("Please Select Date Of Birth"),
  sin: yup.string().required("Sin is Required!"),
  hire_date: yup.string().required("Please Select Hire Date"),
  street: yup.string().required("Street is Required!"),
  city: yup.string().required("City is Required!"),
  province: yup.string().required("Province is Required!"),
  mark_as: yup.string().required("mark as is Required!"),
  color: yup.string().required("Please Select a Color"),
  phone_number: yup.string().required("Phone Number is Required!"),
  postal_code: yup.string().max(10).required("Postal Code is Required!"),
});

/* 
  ------------------
  - Create Sub Event
  ------------------
*/
export const subEvent = yup.object().shape({
  // event_name: yup.string().required("Event Name is Required!"),
  status: yup.string().required("Please Select Status"),
  event_duedate: yup.string().required("Please Select Event Due Date"),
  job_code_id: yup.string().required("Please Select JobCode"),
  emp_id: yup.string().required("Please Select Employee"),
  event_time: yup.string().required("Please Select Event Time"),
  event_estimate_hour: yup
    .string()
    .required("Event Estimate Hour is Required!"),
  reviwer_id: yup.string().required("Please Select Reviewer"),
  filed_date: yup.string().required("Filed Date is Required"),
});

/* 
  ------------------
  - Add and Edit Event
  ------------------
*/
export const eventValidation = yup.object().shape({
  // event_name: yup.string().required("Event Name is Required!"),
  status: yup.string().required("Please Select Status"),
  client_id: yup.string().required("Please Select Client"),
  job_code_id: yup.string().required("Please Select JobCode"),
  allow_extension: yup.string().required("Please Select Allow Extension"),
  event_repeate: yup.string().required("Please Select Event Repeate"),
  map_folder: yup
    .array()
    .min(1, "Please Select atleast one folder.")
    .required("Please Select atleast one folder."),
  // event_description: yup.string().required("Event Description is Required!"),
  event_duedate: yup.string().required("Please Select Event Due Date"),

  // -------------------
  // When Status is "required"
  // -------------------

  emp_id: yup
    .string()
    .nullable()
    .when("status", {
      is: (val) => val === "For Review" || val === "Review Completed",
      then: yup
        .string("Please Select Employee")
        .nullable()
        .required("Please Select Employee"),
    }),
  reviwer_id: yup
    .string()
    .nullable()
    .when("review_required", {
      is: "yes",
      then: yup
        .string("Please Select Reviewer")
        .nullable()
        .required("Please Select Reviewer"),
    }),
  event_time: yup
    .string()
    .nullable()
    .when("status", {
      is: (val) =>
        val === "For Review" || val === "Not Started" || val === "In Progress",
      then: yup.string().nullable().required("Please Select Event Time"),
    }),
  filed_date: yup
    .string()
    .nullable()
    .when("status", {
      is: (val) =>
        val === "For Review" || val === "Not Started" || val === "In Progress",
      then: yup
        .string("PLease select filed Date")
        .nullable()
        .required("PLease select filed Date"),
    }),
  event_estimate_hour: yup
    .string()
    .nullable()
    .when("status", {
      is: (val) =>
        val === "For Review" || val === "Not Started" || val === "In Progress",
      then: yup
        .string()
        .nullable()
        .required("Event Estimate Hour is Required!"),
    }),
});

/* 
  ------------------
  - Add TimeEntryLog
  ------------------
*/
export const timeLog = yup.object().shape({
  event_id: yup.string().required("Please Select Client"),
  start_time: yup.string().required("Please Select Start Time"),
  end_date: yup.string().required("Please Select End Date"),
  // event_id: yup.string().required("Please Select Client"),
  // event_id: yup.string().required("Please Select Client"),
});

/* 
  ------------------
  - Change Password
  ------------------
*/
export const passwordValidation = yup.object().shape({
  current_password: yup
    .string()
    .min(6, "Password Length Should be more than 6 characters")
    .required("Current Password is Required!"),
  new_password: yup
    .string()
    .min(6, "Password Length Should be more than 6 characters")
    .required("New Password is Required!"),
  confirm_password: yup
    .string()
    .min(6, "Password Length Should be more than 6 characters")
    .oneOf([yup.ref("new_password"), null], "Passwords must match!")
    .required("Confirm Password is Required!"),
});

export const emailSettingValidationSchema = yup.object().shape({
  mail_driver: yup.string().required("Driver is Required"),
  mail_host: yup.string().required("Host is Required."),
  mail_port: yup.string().required("Port is Required."),
  mail_username: yup.string().required("Username is Required."),
  mail_encryption: yup.string().required("Encryption is Required."),
  mail_from_name: yup.string().required("From name is Required."),
  // mail_header: yup.string().required("Header is Required."),
  // mail_footer: yup.string().required("Footer is Required."),
});

/* 
  ------------------
  -> client Communication
  ------------------
*/
export const addClientCommunication = yup.object().shape({
  subject: yup.string().required("Subject is Required!"),
  details: yup.string().required("Details is Required!"),
});

export const editClientCommunication = yup.object().shape({
  subject: yup.string().required("Subject Cannot be Empty"),
  details: yup.string().required("Details Cannot be Empty"),
});
