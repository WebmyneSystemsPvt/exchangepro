const logout = async () => {
    setDeleteLoading(true)
    let result = await addRec(`/logout`);
    if (result?.status) {
        localStorage.removeItem("token");
        dispatch(logoutSuccess());
        history.push("/login");
    }
    setDeleteLoading(false)
};