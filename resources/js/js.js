window.onload = function () {

    let overlay = document.getElementById('overlay'),
        langItem = document.querySelectorAll('.lang-item');

    for (let i = 0; i < langItem.length; i++) {
        langItem[i].addEventListener('click', function () {
            [].forEach.call(langItem, function (sl) {
                sl.classList.remove('active');
            });
            this.classList.add('active');
        })
    }

    let forCustomer = document.querySelector(".for-customer"),
        forCustomerSpan = document.querySelector(".for-customer span"),
        forCustomerItem = document.querySelector('.for-customer-items');

    if (forCustomer) {
        forCustomer.addEventListener('click', function () {

            if (forCustomerSpan.classList.contains("open")) {
                forCustomerSpan.classList.remove("open");
                forCustomerItem.classList.remove("open");
                over();
            }
            else {
                forCustomerSpan.classList.add("open");
                forCustomerItem.classList.add("open");
                overlay.classList.add('active');
            }
        });
    }

    if (overlay) {
        overlay.onclick = function () {
            let openClass = document.querySelectorAll(".open");

            for (let i = 0; i < openClass.length; i++) {
                openClass[i].classList.remove('open');
            }
            over();
        };
    }

    function over() {
        overlay.classList.remove('active');
    }

    let account = document.querySelector('.account'),
        personalArea = document.querySelector('.personal-area');

    if (personalArea) {
        personalArea.addEventListener('click', function () {
            if (account.classList.contains("open")) {
                account.classList.remove("open");
                over();
            }
            else {
                account.classList.add("open");
                overlay.classList.add('active');
            }
        });
    }

    let modalButton = document.querySelector('.modal-open'),
        modal = document.querySelector('#modal'),
        modalClose = document.querySelector('.modal-close');

    if (modalButton) {
        modalButton.addEventListener('click', function () {
            if (modal.classList.contains('open')) {
                modal.classList.remove('open');
            }
            else {
                modal.classList.add('open');
            }
        });
    }

    if (modalClose) {
        modalClose.addEventListener('click', function () {
            modal.classList.remove('open');
        });
    }

    let itemHash = document.querySelectorAll('.slide-hash-item');

    for (let i = 0; i < itemHash.length; i++) {
        itemHash[i].addEventListener('click', function () {
            [].forEach.call(itemHash, function (sl) {
                sl.classList.remove('active');
            });
            this.classList.add('active');
        })
    }

    let slideArrow = document.querySelectorAll('.owl-nav span');

    for (let i = 0; i < slideArrow.length; i++) {
        slideArrow[i].addEventListener('click', function () {
            [].forEach.call(slideArrow, function (sl) {
                sl.classList.remove('active');
            });
            this.classList.add('active');
        })
    }

    let filterParams = document.querySelectorAll(".filter-title");

    if(filterParams){
        for(let i = 0; i < filterParams.length; i++){
            filterParams[i].addEventListener('click', function() {
                if(filterParams[i].classList.contains('filter-param-open')){
                    this.classList.remove('filter-param-open');
                }
                else{
                    this.classList.add('filter-param-open');
                }
            })
        }
    }
};

$(document).ready(function () {
    let rightMenu = $('#right-menu'),
        rigthMenuShow = false,
        scrollTop = $('.scroll-top');


    $(document).on('scroll', function () {
        let scroll = $(this).scrollTop();

        if (!rigthMenuShow && scroll > 500) {
            rightMenu.fadeIn(500);
            rigthMenuShow = true;
        }
        else if (rigthMenuShow && scroll < 500) {
            rightMenu.fadeOut(500);
            rigthMenuShow = false;
        }
    });

    scrollTop.on('click', function () {
        $('html, body').animate({scrollTop: 0}, 500);
    });
});
