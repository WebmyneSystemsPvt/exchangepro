import Swal from 'sweetalert2';
import withReactContent from 'sweetalert2-react-content';

const MySwal = withReactContent(Swal);

export const WarningModal = async ({
    title = 'Title',
    text = 'Message',
    icon = 'info',
    showConfirmButton = true,
    confirmButtonText = 'OK',
    showCancelButton = false,
    cancelButtonText = 'Cancel',
}) => {
    const result = await MySwal.fire({
        title,
        text,
        icon,
        showConfirmButton,
        confirmButtonText,
        showCancelButton,
        cancelButtonText,
    });

    return result;
};

