import { toast } from "react-toastify";

export function SuccessToast(msg) {
    // return toast(msg, { position: "bottom-left" });
    return toast.success(msg);
}

export function ErrorToast(msg) {
    return toast.error(msg);
}
