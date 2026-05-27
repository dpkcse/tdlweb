function initThumbsSlider() {
    const swiperContainer = document.querySelector('.wpte-trips-slider__thumbs-wrapper');
    if(null !== swiperContainer) {
        var thumbsSwiper = new elementorFrontend.utils.swiper(swiperContainer, {
            slidesPerView: 1,
        });
    }
}
jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/wptravelengine-trips-slider-three.default",
        initThumbsSlider
    );
});