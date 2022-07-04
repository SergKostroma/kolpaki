export class DetailModal {
    type = 'pc';
    productId;
    modal;
    htmlContent;

    constructor(productId, modal) {
        this.productId = productId;
        this.modal = modal;
        this.checkType();
        this.content().then((r) => {
            this.show()
        });

        if (this.type != 'pc') {
            this.goToCart();
        }
    }
    show() {
        this.modal.html(this.htmlContent);

        if (this.type == 'pc') {
            this.modal.modal('show');
        } else {
            this.modal.addClass('product-info-mobile--shown');
        }
    }

    checkType() {
        this.type = this.modal.hasClass('product-info-modal') ? 'pc' : 'mobile';
    }

    goToCart() {
        const $this = this;
       this.modal.on('click', '.js-open-basket', function () {
           $this.modal.removeClass('product-info-mobile--shown');
            $('.side-basket-cosen').css('display', 'block');
            $('.side-basket-cosen').css('left', '0');
            $('.side-basket-cosen').css('width', '100%');
        })
    }

    async content() {
        const $this = this;
        return await $.ajax({
            url: '/ajax.php',
            dataType: 'json',
            type: 'POST',
            data: {
                'action': 'productDetail',
                'data': {'id': this.productId, 'type': this.type},
            },
            error: function (data) {
                console.log(data);
            },
        }).done(function (data) {
            $this.htmlContent = data;
        });
    }
}




const formModal = document.querySelector('.form-modal')

const openFormButton = document.querySelector('.open-form-button')

const closeFormButton = document.querySelector('.js-close-form-btn')

// document.addEventListener('click',outsideClicker);
// function outsideClicker (event){
//      if(!($(event.target).parents('modal-block').length) && !formModal.classList.contains('hidden')) {
//         formModal.classList.add('hidden')
//         console.log(event.target)
//      }
//     };

closeFormButton.addEventListener('click', formClosed);
function formClosed(event){  
       formModal.classList.add('hidden')  
}

openFormButton.addEventListener('click', formOpener);
 function formOpener(event){
         formModal.classList.remove('hidden')
}






const successModal = document.querySelector('.js-success-modal');

const toSuccessModalBtn = document.querySelector('.js-to-success-modal-btn');

toSuccessModalBtn.addEventListener ('click', successModalOpened);
function successModalOpened(event) {
    console.log(toSuccessModalBtn);
    formModal.classList.add('hidden'); 
    successModal.classList.remove('hidden'); 
} 





const closeSuccessButton = document.querySelector('.js-close-success-modal');
const moreButton = document.querySelector('.js-more-button');

moreButton.addEventListener ('click', successModalClosed);
closeSuccessButton.addEventListener ('click', successModalClosed);


function successModalClosed(event){
    successModal.classList.add('hidden')  
}






$('body').on('click', '.js-payment-method-selection', function (event) {
        
        //let container = $(this).closest('.js-product-info');
       // const $payVarianti = container.find('.order-modal__payment'),
          
          const  $paySelectionButtons = $('.order-modal__payment-method')
            //$payMethodSwitcher = $('.order-modal__payment')
            
        const clickedPayButton = $(this)[0]
        $paySelectionButtons.each((idx, button) => {
            const $payButton = $(button)

            if (clickedPayButton === $payButton[0]) {
                $payButton.addClass('active')
                //$paySelectionButtons[0].style.transform = `translate(${100 * idx}%, 0)`
            } else {
                $payButton.removeClass('active')
            }
        })
        })
    





// const orderSubmit = document.querySelector('.js-order-submit-buttin');
// orderSubmit.addEventListener('click', dangered );
// function dangered(event){
//     alert('Hey!')
// }



//     $(document).on('click', function(e){
//        if (!(
//        ($(e.target).parents('.order-modal').length)
//        ||	($(e.target).hasClass('js-close-form-btn'))
//       )
//        ) {

//    		$('.modal-block').addClass('hidden');
//        }
//    });