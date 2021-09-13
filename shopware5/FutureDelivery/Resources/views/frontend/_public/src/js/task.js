console.log('javascript file is working x start');

/*for quatity 2*/
var sQuantity2Span = document.getElementById('sQuantity2Span');
if(sQuantity2Span)
{
	sQuantity2Span.addEventListener('DOMSubtreeModified', setInputQuantity2);
}

function setInputQuantity2(e)
{	
	document.getElementById('sQuantity2').value = Number(sQuantity2Span.innerText);
}


/*for quatity 3*/
var sQuantity3Span = document.getElementById('sQuantity3Span');
if(sQuantity3Span)
{
	sQuantity3Span.addEventListener('DOMSubtreeModified', setInputQuantity3);
}

function setInputQuantity3(e)
{	
	document.getElementById('sQuantity3').value = Number(sQuantity3Span.innerText);
}


$(document).ready(function(){
	document.querySelectorAll(".sQuantitySpan").forEach(function(el){
		el.addEventListener('DOMSubtreeModified', function(e){
			if(e.srcElement.innerHTML != '')
			{
				$(e.srcElement).parent().prev().val(Number(e.srcElement.innerHTML));
			}
		});
	});
});

function cartButtonDisabler()
{
	
	console.log("running cartButtonDisabler");
	let selectedQuantity = [];
	document.querySelectorAll(".buybox--quantity > .vm-box-product-quantity > span").forEach(function(x){
		selectedQuantity.push(x.innerHTML);
	});
	
	selectedQuantity = [... new Set(selectedQuantity)];
	if(selectedQuantity.length > 0)
	{
		// Getting sum of numbers
		let sum = selectedQuantity.reduce(function(a, b){
			return Number(a) + Number(b);
		});
		
		//console.log(selectedQuantity,sum);
		
		if(sum > 0)
		{
			document.querySelectorAll(".isf_buy_button_container > button.buybox--button").forEach(function(x){
				x.classList.remove("is--disabled");
				x.removeAttribute("disabled");
			});
		}
		else
		{
			document.querySelectorAll(".isf_buy_button_container > button.buybox--button").forEach(function(x){
				x.classList.add("is--disabled");
				x.setAttribute("disabled", "disabled");
			});
		}
	}

}


if(document.querySelectorAll(".buybox--quantity > .vm-box-product-quantity > span").length > 0)
{
	document.querySelectorAll(".buybox--quantity > .vm-box-product-quantity > span").forEach(function(x){
		x.addEventListener('DOMSubtreeModified', cartButtonDisabler)
	});
}

function ButtonDisabler(el)
{
	setTimeout(function(){  
	
		let isf_btn = el.target.parentElement.nextElementSibling;
		//console.log(el.target.innerHTML,"inner html ");
		let val = el.target.innerHTML.trim();
		if(val > 0 && val != '')
		{
				console.log(isf_btn,"if is working");
				isf_btn.classList.remove("is--disabled");
				isf_btn.removeAttribute("disabled");
		}
		else
		{
				console.log(isf_btn,"else is working");
				isf_btn.classList.add("is--disabled");
				isf_btn.setAttribute("disabled", "disabled");
		}

	}, 1000);

}

/*if(!document.getElementById('sQuantity2') && !document.getElementById('sQuantity3'))
{
	if(document.querySelectorAll(".vm-box-product-quantity > span").length > 0)
	{
		document.querySelectorAll(".vm-box-product-quantity > span").forEach(function(x){
			x.addEventListener('DOMSubtreeModified', ButtonDisabler)
		});
	}



	if(document.querySelectorAll(".vm-box-product-quantity > span").length > 0)
	{
		document.querySelectorAll(".vm-box-product-quantity > span").forEach(function(e){
			if(e.innerHTML == 0){
				let isf_btn = e.parentElement.nextElementSibling;
				isf_btn.classList.add("is--disabled");
				isf_btn.setAttribute("disabled", "disabled");
			}
		});
	}	
}*/


$.subscribe('plugin/swEmotionLoader/onLoadEmotionFinished', function(e){
	if(!document.getElementById('sQuantity2') && !document.getElementById('sQuantity3'))
	{
		if(document.querySelectorAll(".vm-box-product-quantity > span").length > 0)
		{
			document.querySelectorAll(".vm-box-product-quantity > span").forEach(function(x){
				x.addEventListener('DOMSubtreeModified', ButtonDisabler)
			});
		}



		if(document.querySelectorAll(".vm-box-product-quantity > span").length > 0)
		{
			document.querySelectorAll(".vm-box-product-quantity > span").forEach(function(e){
				if(e.innerHTML == 0){
					let isf_btn = e.parentElement.nextElementSibling;
					isf_btn.classList.add("is--disabled");
					isf_btn.setAttribute("disabled", "disabled");
				}
			});
		}	
	}       
});



$(document).ready(function(){
	cartButtonDisabler();
});

function formSubmitStoper(e) {
  e.preventDefault();
  return false;
}


console.log('javascript file is working x end');