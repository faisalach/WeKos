let tinder = document.querySelector('.tinder'),
tinderCards = document.querySelector('.tinder--cards'),
loaderContainer = document.querySelector('.loader-container');

let cards = [],
coordsList,
noCards;
var uid1 = document.querySelector('.uid').value;
/*userLat = document.querySelector('.latitude').value,
userLong = document.querySelector('.longitude').value;*/

//fetch all cards
let cardsRequest = new FormData();
cardsRequest.append('get_cards', 'yes');
cardsRequest.append('uid1', uid1);
$.ajax({
	url: 'controllerUserData.php',
	data: cardsRequest,
	processData: false,
	contentType: false,
	type: 'POST',
	success: (res) => {
		cards = JSON.parse(res);
		noCards = cards.pop().noCards;
		if (!noCards) {
			loadCards(cards);
			let script = document.createElement('script');
			script.setAttribute('src', './js/main.js');
			document.body.appendChild(script);
		}else{
			displayCardsOver();
		}
	},
});

async function calcDistance(coordsList) {
	const res = await fetch(
		`https://dev.virtualearth.net/REST/v1/Routes/DistanceMatrix?origins=${userLat},${userLong}&destinations=${coordsList}&travelMode=driving&key=AlqYwMSv8W6JrEnyQg_58Mkj6ZcBFipQI9ToM0_BX4FBTVbuXenxTnpKrNQDQ2G3`
		);
	const json = await res.json();
	return json;
}

function loadCards(cards) {
	let output = '';

	cards.forEach((card) => {
		output += `
		<div class="tinder--card" style="background: linear-gradient(0deg, rgba(0,0,0,0.30885857761073177) 0%, rgba(255,255,255,0) 100%), url(${card.profile_photo}); background-repeat: no-repeat; background-position: center center; background-size: cover;" onmouseover="showBio(this);" onmouseout="hideBio(this);">
		<div class="card-info" data-uid="${card.uid}" data-uid1="${uid}">
		<h4>${card.name.split(' ')[0]}<span class="lead">, ${card.age}</span></h4>
		<p>${card.jurusan != null ? card.jurusan : ''}</p>
		</div>
		<button data-id='${card.uid}' style='position:absolute;left:20px;bottom:80px;' class='btn-profile btn-sm btn btn-light'>Profile</button>
		</div>
		`;
	});
	tinderCards.innerHTML += output;
	tinder.innerHTML += `
	<div class="tinder--buttons">
		<div class="tinder--buttons-container clear-both">
			<button id="nope" class="ml-5 float-left bg-danger text-white"><i class="fas fa-times"></i></button>
			<button id="love" class="mr-5 float-right bg-primary text-white"><i class="fas fa-check"></i></button>
			<!-- <div class="hover-msg lead">Hover to see more!</div> -->
		</div> 
	</div> 
	`;

	tinderCards.innerHTML += output;
}

function displayCardsOver() {
	//hide like-dislike buttons
	$('.tinder').hide();
	//display msg
	$('.cards-over').show();
}
