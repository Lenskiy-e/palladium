"use strict";

window.onload = function () {
  var overlay = document.getElementById('overlay'),
      langItem = document.querySelectorAll('.lang-item');

  for (var i = 0; i < langItem.length; i++) {
    langItem[i].addEventListener('click', function () {
      [].forEach.call(langItem, function (sl) {
        sl.classList.remove('active');
      });
      this.classList.add('active');
    });
  }

  var forCustomer = document.querySelector(".for-customer"),
      forCustomerSpan = document.querySelector(".for-customer span"),
      forCustomerItem = document.querySelector('.for-customer-items');

  if (forCustomer) {
    forCustomer.addEventListener('click', function () {
      if (forCustomerSpan.classList.contains("open")) {
        forCustomerSpan.classList.remove("open");
        forCustomerItem.classList.remove("open");
        over();
      } else {
        forCustomerSpan.classList.add("open");
        forCustomerItem.classList.add("open");
        overlay.classList.add('active');
      }
    });
  }

  if (overlay) {
    overlay.onclick = function () {
      var openClass = document.querySelectorAll(".open");

      for (var _i = 0; _i < openClass.length; _i++) {
        openClass[_i].classList.remove('open');
      }

      over();
    };
  }

  function over() {
    overlay.classList.remove('active');
  }

  var account = document.querySelector('.account'),
      personalArea = document.querySelector('.personal-area');

  if (personalArea) {
    personalArea.addEventListener('click', function () {
      if (account.classList.contains("open")) {
        account.classList.remove("open");
        over();
      } else {
        account.classList.add("open");
        overlay.classList.add('active');
      }
    });
  }

  var modalButton = document.querySelector('.modal-open'),
      modal = document.querySelector('#modal'),
      modalClose = document.querySelector('.modal-close');

  if (modalButton) {
    modalButton.addEventListener('click', function () {
      if (modal.classList.contains('open')) {
        modal.classList.remove('open');
      } else {
        modal.classList.add('open');
      }
    });
  }

  if (modalClose) {
    modalClose.addEventListener('click', function () {
      modal.classList.remove('open');
    });
  }

  var itemHash = document.querySelectorAll('.slide-hash-item');

  for (var _i2 = 0; _i2 < itemHash.length; _i2++) {
    itemHash[_i2].addEventListener('click', function () {
      [].forEach.call(itemHash, function (sl) {
        sl.classList.remove('active');
      });
      this.classList.add('active');
    });
  }

  var slideArrow = document.querySelectorAll('.owl-nav span');

  for (var _i3 = 0; _i3 < slideArrow.length; _i3++) {
    slideArrow[_i3].addEventListener('click', function () {
      [].forEach.call(slideArrow, function (sl) {
        sl.classList.remove('active');
      });
      this.classList.add('active');
    });
  }

  var filterParams = document.querySelectorAll(".filter-title");

  if (filterParams) {
    var _loop = function _loop(_i4) {
      filterParams[_i4].addEventListener('click', function () {
        if (filterParams[_i4].classList.contains('filter-param-open')) {
          this.classList.remove('filter-param-open');
        } else {
          this.classList.add('filter-param-open');
        }
      });
    };

    for (var _i4 = 0; _i4 < filterParams.length; _i4++) {
      _loop(_i4);
    }
  }
};

$(document).ready(function () {
  var rightMenu = $('#right-menu'),
      rigthMenuShow = false,
      scrollTop = $('.scroll-top');
  $(document).on('scroll', function () {
    var scroll = $(this).scrollTop();

    if (!rigthMenuShow && scroll > 500) {
      rightMenu.fadeIn(500);
      rigthMenuShow = true;
    } else if (rigthMenuShow && scroll < 500) {
      rightMenu.fadeOut(500);
      rigthMenuShow = false;
    }
  });
  scrollTop.on('click', function () {
    $('html, body').animate({
      scrollTop: 0
    }, 500);
  });
});