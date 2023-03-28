(function ($) {
  "use strict";

  $(document).ready(function () {
    /*------------------
        Hero Slider
        ------------------*/
    let heroSlider = $(".hero-slider");
    if (heroSlider.length) {
      heroSlider.owlCarousel({
        items: 1,
        dots: false,
        loop: true,
        autoplay: true,
        autoplayHoverPause: true,
        smartSpeed: 3000,
      });
    }

    /*------------------
        Hamburger Menu Toggle
        ------------------*/
    let mobileMenu = $(".zol-menu-toggle");
    let menuIs = $(".zol-menu");
    let menuOpen = $(".zol-menu-open");
    let menuClose = $(".zol-menu-close");
    menuOpen.on("click", function () {
      $(this).addClass("d-none");
      menuClose.addClass("active");
      menuIs.slideToggle();
    });
    menuClose.on("click", function () {
      menuIs.slideToggle();
      $(this).removeClass("active");
      menuOpen.removeClass("d-none");
    });
  });

  /*------------------
        Menu
    ------------------*/
  let menuSub = $(".zol-menu__sub");
  let menuHasSub = $(".zol-menu__has-sub");
  let menuLing = $(".zol-menu__link");
  $(".zol-menu__has-sub > .zol-menu__link").on("click", function (e) {
    e.preventDefault();
  });
  $(".zol-menu__has-sub-2 > .zol-menu__sub-link").on("click", function (e) {
    e.preventDefault();
  });
  /*------------------
        back to top
    ------------------*/
  $(document).on("click", ".back-to-top", function () {
    $("html,body").animate(
      {
        scrollTop: 0,
      },
      2000
    );
  });
})(jQuery);

$(window).on("scroll", function () {
  var ScrollTop = $(".back-to-top");
  if ($(window).scrollTop() > 1200) {
    ScrollTop.fadeIn(1000);
  } else {
    ScrollTop.fadeOut(1000);
  }
});

$(window).on("load", function () {
  /*-----------------
        preloader
    ------------------*/
  var preLoder = $(".preloader");
  preLoder.fadeOut(1000);
});
/*------------------
    Animation on Scroll
------------------*/
sal({
  threshold: 0.5,
  // once: false,
});

$(".t-link").click(function () {
  var sectionTo = $(this).attr("href");
  $("html, body").animate(
    {
      scrollTop: $(sectionTo).offset().top,
    },
    1500
  );
});
