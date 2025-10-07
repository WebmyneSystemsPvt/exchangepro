// includes Script
$(function() {
    $("#header").load("header.html");
    $("#footer").load("footer.html");
});

// $('#popular').owlCarousel({
//     loop: true,
//     margin: 10,
//     nav: true,
//     dots: false,
//     items: 1,
//     autoplay: true,
//     navigation: true,
//     navigationText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
    
// })
('#popular').owlCarousel({
    autoplay: true,
    autoplayTimeout: 5000,
    navigation: false,
    margin: 10,
    responsive: {
        0: {
            items: 1
        },
        600: {
            items: 2
        },
        1000: {
            items: 2
        }
    }
})


$(document).ready(function () {
    // slick carousel
    $('.slider1').slick({
        dots: true,
        vertical: true,
        arrows: false,
        dots: false,
        autoplay: false,
        slidesToShow: 5,
        slidesToScroll: 1,
        verticalSwiping: true,
    });
    $('.slider2').slick({
        dots: true,
        vertical: true,
        arrows: false,
        dots: false,
        autoplay: false,
        slidesToShow: 5,
        slidesToScroll: 1,
        verticalSwiping: true,
    });
    $('.slider3').slick({
        dots: true,
        vertical: true,
        arrows: false,
        dots: false,
        autoplay: false,
        slidesToShow: 5,
        slidesToScroll: 1,
        verticalSwiping: true,
    });
    $('.slider4').slick({
        dots: true,
        vertical: true,
        arrows: false,
        dots: false,
        autoplay: false,
        slidesToShow: 5,
        slidesToScroll: 1,
        verticalSwiping: true,
    });
    $('.slider5').slick({
        dots: true,
        vertical: true,
        arrows: false,
        dots: false,
        autoplay: false,
        slidesToShow: 5,
        slidesToScroll: 1,
        verticalSwiping: true,
    });

    const interval = setInterval(function () {
        var myArray = ['slider1', 'slider2', 'slider3', 'slider4', 'slider5'];
        const random = Math.floor(Math.random() * myArray.length);

        $('.' + myArray[random]).slick('slickNext');
    }, 5000);



});

$(".mob_fiterbtn").click(function () {
    alert("123");
    $(".sidebar").toggleClass("show");
});