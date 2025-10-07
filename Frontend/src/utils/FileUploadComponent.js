import React, { useEffect, useRef, useState } from 'react';
import { ErrorToast } from './Toaster';

const ImageUploader = ({ FileUploadChange, isSingle, ButtonName }) => {
    const [images, setImages] = useState([]);
    const MAX_FILE_SIZE_MB = 2;
    const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;

    useEffect(() => {
        let FilterFileOnly = images?.map((image) => { return image?.file });
        FileUploadChange(FilterFileOnly)
    }, [FileUploadChange, images],)

    const fileInputRef = useRef(null);

    const handleFileChange = (e) => {


        if (isSingle) {
            const fileList = e.target.files;
            const newImages = [];
            for (let i = 0; i < fileList.length; i++) {
                const file = fileList[i];

                if (file.size > MAX_FILE_SIZE_BYTES) {
                    ErrorToast('File size exceeds 2 MB');
                    continue; // Skip this file and continue with the next
                }

                const src = URL.createObjectURL(file);
                newImages.push({ file, src });
            }
            setImages([...newImages]);
            fileInputRef.current.value = null;
            // Reset the file input
        } else {
            const files = e.target.files;
            const oldFiles = images;
            const uploadedImages = [...oldFiles]; // Copy the old files to avoid mutation

            // Iterate over each selected file
            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (file.size > MAX_FILE_SIZE_BYTES) {
                    ErrorToast('File size exceeds 2 MB');
                    continue; // Skip this file and continue with the next
                }

                const reader = new FileReader();

                // reader.onload = (e) => {
                //     const img = new Image();
                //     img.src = e.target.result;

                //     img.onload = () => {
                //         const canvas = document.createElement('canvas');
                //         const ctx = canvas.getContext('2d');

                //         // Set canvas size to image size
                //         canvas.width = img.width;
                //         canvas.height = img.height;

                //         // Draw the image onto the canvas
                //         ctx.drawImage(img, 0, 0);

                //         // Calculate the font size
                //         const fontSize = 24;
                //         ctx.font = `${fontSize}px Arial`;
                //         ctx.fillStyle = 'white';
                //         ctx.textAlign = 'left'; // Align text to the left

                //         // Add the last modified date as the timestamp
                //         const lastModifiedDate = new Date(file.lastModified);
                //         const timestamp = lastModifiedDate.toLocaleString();

                //         // Position text 50px from the left and at the bottom of the canvas
                //         const textMargin = 50; // Margin from the left edge
                //         const y = canvas.height - textMargin + fontSize; // Margin from the bottom edge

                //         // Draw the text
                //         ctx.fillText(timestamp, textMargin, y);

                //         // Get the binary form of the processed image
                //         canvas.toBlob((blob) => {
                //             const reader = new FileReader();
                //             reader.onloadend = () => {
                //                 const arrayBuffer = reader.result;
                //                 const processedFile = new File([arrayBuffer], `${file.name.split('.')[0]}_timestamped.png`, {
                //                     type: 'image/png',
                //                 });

                //                 uploadedImages.push({
                //                     src: URL.createObjectURL(processedFile),
                //                     processedBinary: arrayBuffer,
                //                     file: processedFile
                //                 });

                //                 // Update state with uploaded images
                //                 setImages([...uploadedImages]);
                //             };
                //             reader.readAsArrayBuffer(blob);
                //         }, 'image/png');
                //     };
                // };

                // 
                // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
                // 

                reader.onload = (e) => {
                    const img = new Image();
                    img.src = e.target.result;

                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');

                        // Set canvas size to image size
                        canvas.width = img.width;
                        canvas.height = img.height;

                        // Draw the image onto the canvas
                        ctx.drawImage(img, 0, 0);

                        // Calculate the font size
                        const fontSize = 24;
                        ctx.font = `${fontSize}px Arial`;
                        ctx.fillStyle = 'white';
                        ctx.textAlign = 'left'; // Align text to the left

                        // Add the last modified date as the timestamp
                        const lastModifiedDate = new Date(file.lastModified);
                        const timestamp = lastModifiedDate.toLocaleString();

                        // Calculate the x-coordinate for text to be 20% from the left side
                        const textMargin = canvas.width * 0.1; // 20% from the left edge
                        const y = canvas.height - fontSize - 10; // 10 pixels from the bottom edge

                        // Draw the text
                        ctx.fillText(timestamp, textMargin, y);

                        // Get the binary form of the processed image
                        canvas.toBlob((blob) => {
                            const reader = new FileReader();
                            reader.onloadend = () => {
                                const arrayBuffer = reader.result;
                                const processedFile = new File([arrayBuffer], `${file.name.split('.')[0]}_timestamped.png`, {
                                    type: 'image/png',
                                });

                                uploadedImages.push({
                                    src: URL.createObjectURL(processedFile),
                                    processedBinary: arrayBuffer,
                                    file: processedFile
                                });

                                // Update state with uploaded images
                                setImages([...uploadedImages]);
                            };
                            reader.readAsArrayBuffer(blob);
                        }, 'image/png');
                    };
                };

                // Read the file as a data URL
                reader.readAsDataURL(file);
            }
            fileInputRef.current.value = null;
        }
    };

    const handleDeleteImage = (index) => {
        setImages(images.filter((_, i) => i !== index));
    };

    const handleDragStart = (e, index) => {
        e.dataTransfer.setData('index', index.toString());
    };

    const handleDragOver = (e) => {
        e.preventDefault();
    };

    const handleDrop = (e, dropIndex) => {
        e.preventDefault();
        const dragIndex = Number(e.dataTransfer.getData('index'));
        const newImages = [...images];
        const [draggedImage] = newImages.splice(dragIndex, 1);
        newImages.splice(dropIndex, 0, draggedImage);
        setImages(newImages);
    };

    return (
        <div className='upliad_img'>
            <input
                type="file"
                accept="image/*"
                multiple={isSingle ? false : true}
                onChange={(e) => handleFileChange(e)}
                ref={fileInputRef}
                style={{ display: 'none' }}
            />
            <button onClick={() => fileInputRef.current.click()}>{ButtonName}</button>
            <div className="image-container">
                {images.map((image, index) => (
                    <div
                        key={index}
                        className="image-item"
                        draggable
                        onDragStart={(e) => handleDragStart(e, index)}
                        onDragOver={handleDragOver}
                        onDrop={(e) => handleDrop(e, index)}
                    >
                        <img src={image.src} alt={`${index}`} />
                        <button onClick={() => handleDeleteImage(index)}>Delete</button>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default ImageUploader;
