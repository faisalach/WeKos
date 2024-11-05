<?php require_once "controllerUserData.php"; ?>
<?php include_once 'header.php'; ?>
<?php include_once 'navbar.php'; ?>

	<!-- HIDDEN INPUTS  -->
	<input type="text" class="uid1-name" hidden value="<?php echo $name; ?>">
	<input type="text" class="uid" hidden value="<?php echo $_SESSION['uid1']; ?>">

	<!-- REDIRECT TO MATCH FORM -->
	<form action="match.php" method="POST" id="match-redirect-form" hidden>
		<input type="text" id="match-redirect-uid1" name="match-redirect-uid1" value="<?php echo $uid; ?>">
		<input type="text" id="match-redirect-uid2" name="match-redirect-uid2">
	</form>

	<!-- CARDS -->
	<div class="tinder" id="tinder-container">
		<div class="tinder--status">
			<i class="fas fa-times"></i>
			<i class="fas fa-check"></i>
		</div>
		<div class="tinder--cards"></div>
	</div>

	<div class="cards-over " style="display: none;" id="cards-over-container">
		<div class="jumbotron">
			<h1 class="display-4">Sorry!</h1>
			<p class="lead">You've seen all potential matches in your search</p>
			<hr class="my-4">
			<p>Try again with a different combination to see if there are more</p>
			<p class="lead">
				<a class="btn btn-primary btn-lg" href="#" role="button">Search <i class="fas fa-search"></i></a>
			</p>
		</div>
	</div>

	<!-- Modal -->
	<style>
		#profile-img{
			height: 50vh;
			width: 100%;
			object-position: center;
			object-fit: cover;
		}

		.modal-profile .container-text .rounded{
			background-color: #cbcbcb;
		}
	</style>
	<div class="modal fade modal-profile" data-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body p-0">
					<button type="button" class="close position-absolute" style="right: 10px;top: 10px;font-size: 2rem;color: white;" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<img src="" id="profile-img">
					<div class="p-3 container-text">
						<div class="p-3 rounded">
							<h4 id="nama">Nama</h4>
							<div class="d-flex">
								<h6 id="asal_kota">Asal Kota</h6>
								<div class="mx-2">|</div>
								<h6 id="jurusan">Jurusan</h6>
							</div>
						</div>

						<h5 class="h5 font-weight-bold mt-3">About</h5>
						<div class="form-group">
							<label for="" class="font-weight-bold">Tanggal Lahir</label>
							<p id="birth_date"></p>
						</div>
						<div class="form-group">
							<label for="" class="font-weight-bold">Tinggi / Berat</label>
							<p id="height_weight"></p>
						</div>
						<div class="form-group">
							<label for="" class="font-weight-bold">Asal Kota</label>
							<p id="asal_kota_2"></p>
						</div>
						<div class="form-group">
							<label for="" class="font-weight-bold">Jurusan</label>
							<p id="jurusan_2"></p>
						</div>
						<div class="form-group">
							<label for="" class="font-weight-bold">Organisasi</label>
							<p id="organisasi"></p>
						</div>
						<div class="form-group">
							<label for="" class="font-weight-bold">Sosial Media</label>
							<p>
							
							<i class="fas fa-fw"><svg style="width: 1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M448 209.9a210.1 210.1 0 0 1 -122.8-39.3V349.4A162.6 162.6 0 1 1 185 188.3V278.2a74.6 74.6 0 1 0 52.2 71.2V0l88 0a121.2 121.2 0 0 0 1.9 22.2h0A122.2 122.2 0 0 0 381 102.4a121.4 121.4 0 0 0 67 20.1z"/></svg></i> <span id="tiktok"></span>
							<br>
							<i class="fab fa-fw fa-instagram"></i> <span id="ig"></span>
							<br>
							<i class="fab fa-fw fa-twitter"></i> <span id="twitter"></span>
							<br>
							<i class="fab fa-fw fa-facebook"></i> <span id="facebook"></span>
							</p>
						</div>
						<div class="form-group">
							<label for="" class="font-weight-bold">Bio</label>
							<p class="text-justify" id="bio">Lorem ipsum dolor sit amet consectetur adipisicing, elit. Et incidunt debitis quod earum mollitia reprehenderit doloremque excepturi cum aspernatur eveniet.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="./js/home.js"></script>
	<script src="https://hammerjs.github.io/dist/hammer.js"></script>
	<script src="./js/cards.js"></script>
	<script>
		$(() => {
			$("body").on("click",".btn-profile",function(e){
				e.preventDefault();
				let id = $(this).data('id');
				$.ajax({
					url 	: 'controllerUserData.php',
					data 	: {
						uid : id,
						'show-profile' : true
					},
					method 	: "POST",
					dataType: "JSON",
					success	: function(data) {
						$(".modal-profile").modal("show");
						$("#profile-img").attr("src",data.profile_photo);
						$("#nama").html(data.name);
						$("#asal_kota").html(data.nama_kota);
						$("#jurusan").html(data.nama_jurusan);
						$("#birth_date").html(data.birth_date);
						$("#height_weight").html(data.height + " cm / "+data.weight+" kg");
						$("#organisasi").html((data.organisasi != "" ? data.organisasi : "-"));
						$("#tiktok").html(data.tiktok != "" ? data.tiktok : "-");
						$("#ig").html(data.ig != "" ? data.ig : "-");
						$("#twitter").html(data.twit != "" ? data.twit : "-");
						$("#facebook").html(data.fb != "" ? data.fb : "-");
						$("#bio").html(data.bio);

						if (data.nama_kota != null && data.nama_provinsi != null) {
							$("#asal_kota_2").html(data.nama_kota+", "+data.nama_provinsi);
						}else{
							$("#asal_kota_2").html("-");
						}

						if (data.nama_jurusan != null && data.nama_fakultas != null) {
							$("#jurusan_2").html(data.nama_jurusan+", Fakultas "+data.nama_fakultas);
						}else{
							$("#jurusan_2").html("-");
						}
					}
				})
			})
		})
	</script>
</body>
</html>