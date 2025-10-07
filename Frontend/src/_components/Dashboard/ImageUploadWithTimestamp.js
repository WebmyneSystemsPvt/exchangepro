import React, { useState } from 'react';
import { useDropzone } from 'react-dropzone';
import { Box, Button, CircularProgress, Grid, Paper, Typography } from '@mui/material';
import { Loader } from '../../common/Loader';

const ImageUploadWithTimestamp = () => {
    const [images, setImages] = useState([]);
    const [loader, setLoader] = useState(false);

    const handleFileInputChange = (event) => {
        const files = event.target.files;
        uploadFiles(files);
    };

    const uploadFiles = (files) => {
        setLoader(true);
        const uploadedImages = [];

        // Iterate over each selected file
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = (e) => {
                const img = new Image();
                img.src = e.target.result;

                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = img.width;
                    canvas.height = img.height;

                    // Draw the image onto the canvas
                    ctx.drawImage(img, 0, 0);

                    // Calculate the font size as a percentage of the image size
                    const fontSize = Math.min(canvas.width, canvas.height) * 0.05; // 5% of the smallest dimension
                    ctx.font = `${fontSize}px Arial`;
                    ctx.fillStyle = 'white';
                    ctx.textAlign = 'right';

                    // Add the last modified date as the timestamp
                    const lastModifiedDate = new Date(file.lastModified);
                    const timestamp = lastModifiedDate.toLocaleString();
                    ctx.fillText(timestamp, canvas.width - 10, fontSize);

                    // Get the data URL of the processed image
                    const dataUrl = canvas.toDataURL('image/png');

                    // Get the binary form of the processed image
                    canvas.toBlob((blob) => {
                        const reader = new FileReader();
                        reader.onloadend = () => {
                            const arrayBuffer = reader.result;
                            const processedFile = new File([arrayBuffer], `${file.name.split('.')[0]}_timestamped.png`, {
                                type: 'image/png',
                            });

                            // Simulate API upload (replace with actual API call)
                            setTimeout(() => {
                                uploadedImages.push({
                                    original: URL.createObjectURL(file),
                                    processed: dataUrl,
                                    processedBinary: arrayBuffer,
                                });

                                // Update state with uploaded images
                                setImages([...uploadedImages]);
                                setLoader(false);
                            }, 1000); // Simulate delay for processing
                        };
                        reader.readAsArrayBuffer(blob);
                    }, 'image/png');
                };
            };

            // Read the file as a data URL
            reader.readAsDataURL(file);
        }
    };

    const onDrop = (acceptedFiles) => {
        setLoader(true);
        acceptedFiles.forEach((file) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.src = e.target.result;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = img.width;
                    canvas.height = img.height;

                    // Draw the image onto the canvas
                    ctx.drawImage(img, 0, 0);

                    // Add the timestamp
                    const DateStamp = new Date().toLocaleString();
                    const lastModifiedDate = new Date(file.lastModified);
                    const timestamp = lastModifiedDate ? lastModifiedDate.toLocaleString() : DateStamp;


                    // Calculate the font size as a percentage of the image size
                    const fontSize = Math.min(canvas.width, canvas.height) * 0.05; // 5% of the smallest dimension
                    ctx.font = `${fontSize}px Arial`;
                    ctx.fillStyle = 'white';
                    ctx.textAlign = 'right';




                    ctx.fillText(timestamp, canvas.width - 10, fontSize);

                    // Get the data URL of the processed image
                    const dataUrl = canvas.toDataURL('image/png');

                    // Get the binary form of the processed image

                    // Get the binary form of the processed image
                    canvas.toBlob((blob) => {
                        const reader = new FileReader();
                        reader.onloadend = () => {
                            const arrayBuffer = reader.result;
                            const processedFile = new File([arrayBuffer], `${file.name.split('.')[0]}_timestamped.png`, {
                                type: 'image/png',
                            });
                            setImages((prevImages) => [
                                ...prevImages,
                                {
                                    original: URL.createObjectURL(file),
                                    originalFile: acceptedFiles,
                                    processed: dataUrl,
                                    processedBlob: blob,
                                    processedFile,
                                    processedBinary: arrayBuffer
                                },
                            ]);
                        };
                        reader.readAsArrayBuffer(blob);
                    }, 'image/png');


                    // canvas.toBlob((blob) => {
                    //     const reader = new FileReader();
                    //     reader.onloadend = () => {
                    //         const arrayBuffer = reader.result;
                    //         setImages((prevImages) => [
                    //             ...prevImages,
                    //             {
                    //                 original: URL.createObjectURL(file),
                    //                 originalFile: acceptedFiles,
                    //                 processed: dataUrl,
                    //                 processedBinary: arrayBuffer,
                    //                 processedBlob: blob
                    //             },
                    //         ]);
                    //     }
                    // }, 'image/png');


                    // setImages((prevImages) => [
                    //     ...prevImages,
                    //     {
                    //         original: URL.createObjectURL(file),
                    //         processed: dataUrl,
                    //         originalFile: acceptedFiles,
                    //     },
                    // ]);
                };
            };
            reader.readAsDataURL(file);
        });
        setLoader(false);
    };

    const { getRootProps, getInputProps } = useDropzone({
        onDrop,
        accept: 'image/*',
    });

    return (
        <Box sx={{ textAlign: 'center', mt: 4 }}>
            <Box sx={{ border: '2px dashed gray', p: 2, cursor: 'pointer' }}>
                {/* <input {...getInputProps()} disabled={loader} /> */}


                <input
                    type="file"
                    onChange={handleFileInputChange}
                    accept="image/*"
                    style={{ display: 'none' }}
                    id="file-input"
                    multiple
                />
                <label htmlFor="file-input">
                    <Button variant="contained" component="span">
                        Upload Images
                    </Button>
                </label>


                {loader ?
                    <Box mt={2}>
                        <CircularProgress />
                        <Typography>Processing...</Typography>
                    </Box>
                    : <Typography></Typography>
                    // : <Typography>Drag 'n' drop some files here, or click to select files</Typography>
                }
            </Box>
            {!loader && images.length > 0 && (
                <Box mt={4}>
                    <Typography variant="h6">Uploaded Images:</Typography>
                    <Grid container spacing={2} sx={{ p: 2, }}>
                        {images.map((image, index) => (
                            <>
                                <Grid item xs={12} sm={6} key={index}>
                                    <Paper elevation={3} sx={{ p: 2, textAlign: 'center' }}>
                                        <Typography variant="subtitle1">Original Image:</Typography>
                                        <img src={image.original} alt={`Original ${index}`} style={{ maxWidth: '100%' }} />
                                    </Paper>
                                </Grid>
                                <Grid item xs={12} sm={6} key={index}>
                                    <Paper elevation={3} sx={{ p: 2, textAlign: 'center' }}>
                                        <Typography variant="subtitle1">Processed Image:</Typography>
                                        <img src={image.processed} alt={`Processed ${index}`} style={{ maxWidth: '100%' }} />
                                    </Paper>
                                </Grid>
                            </>
                        ))}
                    </Grid>
                </Box>
            )}
        </Box>
    );
};

export default ImageUploadWithTimestamp;
