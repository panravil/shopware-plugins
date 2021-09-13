function giftLimitChecker(){
	if($('#gift-counter-start').text() == '1')
	{
		$('#giftsModal').hide();
		$(".container--ajax-cart.off-canvas").removeClass("isf--is--open is--open");
	}
}

function modal_close()
{
	if(document.querySelector(".container--ajax-cart.off-canvas") != null)
	{
		document.querySelector(".container--ajax-cart.off-canvas").classList.remove('isf--is--open');
		document.querySelector(".container--ajax-cart.off-canvas").classList.remove('is--open');
	}

	if(document.getElementById('giftsModal'))
	{
		document.getElementById("giftsModal").style.display = "none";
	}

	if(document.getElementById('giftsModalcart'))
	{
		document.getElementById("giftsModalcart").style.display = "none";
	}

	if(document.getElementsByClassName('vm--popup-overlay')[0])
	{
		document.getElementsByClassName('vm--popup-overlay')[0].style.display = "none";
	}
}


/*for cart page */
function giftLimitCheckerCart(el)
{
	var x  =  Number(document.getElementById('gift_limit_var').value);

    if(x > 0)
	{
		x = x - 1;
		document.getElementById('gift_limit_var').value = x;
		let y = Number(document.getElementById('gift-counter-start').innerHTML);
		document.getElementById('gift-counter-start').innerHTML = y-1;

        $(el).parent().parent().submit();

		if(x <= 0)
		{
            var url = $(el).attr('data-url');
			setTimeout(function()
            {
                window.location = url;
            }, 500);
		}
	}
}

$(document).ready(function()
{
	if(document.getElementById('giftsModalcart') != null)
	{
		document.getElementById('giftsModalcart').style.display = "block";
		setCarousel();
	}
});

// Get the modal
var modal = document.getElementById("giftsModal");
if(modal != null)
{
	var span = document.getElementsByClassName("giftsClose")[0];
	// When the user clicks on <span> (x), close the modal
	if(span != null)
	{
		span.onclick = function() {
		  modal.style.display = "none";
		}
	}
	
	// When the user clicks anywhere outside of the modal, close it
	$(document).ready(function()
    {
		if(document.getElementById('giftsModal') != null)
		{
			document.getElementById('giftsModal').addEventListener("mousemove", function(){
				document.querySelector(".container--ajax-cart.off-canvas").classList.add('isf--is--open');
			});
		}
	});
}

//gift-counter-start
//gift-counter-end
function check_gift_counter_start(e)
{
	var x = document.getElementById('gift-counter-start');
	var y = document.getElementById('gift-counter-end');
	if(x != null && x.innerHTML != '' && x.innerHTML != 0)
	{
		if(x.innerHTML <= 0 )
		{
			setTimeout(function(){ location.reload(); }, 2000);
		}
	}
}

var gift_counter_start = document.getElementById('gift-counter-start');
if(gift_counter_start)
{
	gift_counter_start.addEventListener('DOMSubtreeModified', check_gift_counter_start);
}


//on cart item delete reload page
function onDeleteCompleteReload()
{
	var proxied = window.XMLHttpRequest.prototype.send;
	window.XMLHttpRequest.prototype.send = function() {
		//Here is where you can add any code to process the request. 
		//If you want to pass the Ajax request object, pass the 'pointer' below
		var pointer = this
		var intervalId = window.setInterval(function(){
				if(pointer.readyState != 4){
						return;
				}
				var url = pointer.responseURL;

                if(url.includes("ajaxDeleteArticleCart") && pointer.status == 200)
				{
					location.reload();
				}
				
				//Here is where you can add any code to process the response.
				//If you want to pass the Ajax request object, pass the 'pointer' below
				clearInterval(intervalId);

		}, 1);//I found a delay of 1 to be sufficient, modify it as you need.
		return proxied.apply(this, [].slice.call(arguments));
	};
}

/*refresh page after product add*/
function refreshAfterProductAdd()
{
	var proxied = window.XMLHttpRequest.prototype.send;
	window.XMLHttpRequest.prototype.send = function() {
		//Here is where you can add any code to process the request. 
		//If you want to pass the Ajax request object, pass the 'pointer' below
		var pointer = this
		var intervalId = window.setInterval(function(){
				if(pointer.readyState != 4){
						return;
				}
				var url = pointer.responseURL;
				if(url.includes("ajaxAddArticleCart") && pointer.status == 200)
				{
					if(document.getElementById('artID'))
					{
						let artID = document.getElementById('artID').value;
						$.ajax({
							method: "POST",
							url: "https://"+window.location.hostname+"/frontend/AjaxMethodController/getGiftByArticleId",
							data: { articleID:artID}
						}).done(function(x) {
                            if(document.getElementById('giftArticlesSection') != null)
                            {
                                document.getElementById('giftArticlesSection').innerHTML = x;
								setTimeout(function() {
									let limitValue = document.getElementById('gift_limit_var').value;
									limitValue = limitValue == '' ? 0 : parseInt(limitValue);
									if(limitValue > 0) {
										document.getElementById('giftsModal').style.display = "block";
										setCarousel();
									} else {
										document.getElementById('giftsModal').style.display = "none";
									}
								}, 100);
                            }
                        });
					}

				}
				
				//Here is where you can add any code to process the response.
				//If you want to pass the Ajax request object, pass the 'pointer' below
				clearInterval(intervalId);

		}, 1);//I found a delay of 1 to be sufficient, modify it as you need.
		return proxied.apply(this, [].slice.call(arguments));
	};
}

/*This function will call whenever ajaxAddArticleCart this function run*/
$(document).ready(function(){
	refreshAfterProductAdd();
});

function setCarousel() {
	var centerStatus = $('.owl-product-slider .owl-item').length > 1 ? true : false;
	$('.owl-product-slider').owlCarousel({
		margin:10,
		dots: false,
		nav:true,
		navText : ["<a class='arrow--prev product-slider--arrow'></a>","<a class='arrow--next product-slider--arrow'></a>"],
		center: centerStatus,
		responsiveClass:true,
		afterAction: function() {
			if ( this.itemsAmount > this.visibleItems.length ) {
				$('.owl-next').show();
				$('.owl-prev').show();
			
				$('.owl-next').removeClass('hide');
				$('.owl-prev').removeClass('hide');
				if ( this.currentItem == 0 ) {
				  $('.owl-prev').addClass('hide');
				}
				if ( this.currentItem == this.maximumItem ) {
				  $('.owl-next').addClass('hide');
				}
			
			} else {
				$('.owl-next').hide();
				$('.owl-prev').hide();
			}
		},
		responsive:{
			0:{
				items:1
			},
			376:{
				items:2
			}
		}
	});
}

function addArticleAjax(el)
{
    var limitValue = parseInt($('#gift-counter-start').text());
    if(limitValue > 0) {
        limitValue --;
        $('#gift-counter-start').text(limitValue);

        var data  =  new Object();
        formUrl = el.parentElement.parentElement.getAttribute('data-addarticleurl');
        dataArray = $(el.parentElement.parentElement).serializeArray();
        dataArray.forEach(function(x){
            data[x.name] = x.value;
        });
    
        $.ajax({
            method: "POST",
            url: formUrl,
            data: data
        }).done(function(x) {
            // document.querySelector(".container--ajax-cart.off-canvas").innerHTML = x;
        });
    }
}

//vm custom close function of gift popup in cart
$(document).ready(function(){
	$("#giftsModalcart .modal-content-cart").prepend("<div class='vm-gift-cart-close'>x</div>");

	$("#giftsModalcart .modal-content-cart .vm-gift-cart-close").click(function(){
		$("#giftsModalcart").hide();
	})
})
