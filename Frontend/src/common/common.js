import axiosConfig from "../utils/axiosConfig";

// export class commonService {
//     static fetchListing = (req) => {
//         return (dispatch) => {
//             axiosConfig.get(req)
//                 .then((res) => {
//                     // console.log(" ~ res:", res)
//                     dispatch(messageText(res.data.message));


//                     if (res.data.status === 200) {
//                         return res.data.data
//                     } else {
//                         return res.data = []
//                     }
//                 }).catch((error) => {
//                     console.log(" error:", error)
//                     // dispatch(loginError("Login error"));
//                 });
//         }
//     };
// }

export const fetchListing = async (params) => {
    return await axiosConfig.get(params)
        .then((res) => {
            if (res?.data) {
                return res?.data
            } else {
                return res?.response?.data
            }
        })
        .catch((error) => console.log(error))
};

export const postFetchListing = async (params) => {
    return await axiosConfig.post(params)
        .then((res) => {
            if (res.data) {
                return res.data
            } else {
                return res.response.data
            }
        })
        .catch((error) => console.log(error))
};

export const postFetch = async (params, data) => {
    return await axiosConfig.post(params, data)
        .then((res) => {
            if (res.data) {
                return res.data
            } else {
                return res.response.data
            }
        })
        .catch((error) => console.log(error))
};

export const getById = async (params) => {
    return await axiosConfig.get(params)
        .then((res) => {
            if (res.data) {
                return res.data
            } else {
                return res.response.data
            }
        })
        .catch((error) => console.log(error))
};

export const cancelEditing = async (params) => {
    return await axiosConfig.get(params)
        .then((res) => {
            if (res.data) {
                return res.data
            } else {
                return res.response.data
            }
        })
        .catch((error) => console.log(error))
};

export const addRec = async (params, data) => {
    return await axiosConfig.post(params, data)
        .then((res) => {
            if (res.data) {
                return res.data
            } else {
                return res.response.data
            }
        })
        .catch((error) => console.log(error))
};

export const downLoadFile = async (params, data) => {
    return await axiosConfig.post(params, data, {
        responseType: 'arraybuffer',
    }).then((res) => {
        if (res) {
            return res
        }
    }).catch((error) => console.log(error))
};

export const editRec = async (params, data) => {
    return await axiosConfig.put(params, data)
        .then((res) => {
            if (res.data) {
                return res.data
            } else {
                return res.response.data
            }
        })
        .catch((error) => console.log(error))
};

export const editRecParamsOnly = async (params) => {
    return await axiosConfig.put(params)
        .then((res) => {
            if (res.data) {
                return res.data
            } else {
                return res.response.data
            }
        })
        .catch((error) => console.log(error))
};

export const deleteRec = async (params) => {
    return await axiosConfig.delete(params)
        .then((res) => {
            if (res.data) {
                return res.data
            } else {
                return res.response.data
            }
        })
        .catch((error) => console.log(error))
};
