<?php require_once "controllerUserData.php"; ?>
<?php require_once "header.php"; ?>
<?php require_once "navbar.php"; ?>

<?php 
$sql = "SELECT * FROM userprofile WHERE uid = '$uid'";
$run_Sql = mysqli_query($con, $sql);
if($run_Sql){
	$data_profile = mysqli_fetch_assoc($run_Sql);
}

$sql = "SELECT * FROM social WHERE uid = '$uid'";
$run_Sql = mysqli_query($con, $sql);
if($run_Sql){
	$data_social = mysqli_fetch_assoc($run_Sql);
}

$sql = "SELECT * FROM provinces";
$data_province = mysqli_query($con, $sql);

$sql = "SELECT * FROM regencies";
$data_kota = mysqli_query($con, $sql);
$data_kota_temp 	= [];
foreach ($data_kota as $row) {
	$data_kota_temp[$row["province_id"]][] = $row;
}
$data_kota 	= $data_kota_temp;

$sql = "SELECT * FROM fakultas";
$data_fakultas = mysqli_query($con, $sql);

$sql = "SELECT * FROM jurusan";
$data_jurusan = mysqli_query($con, $sql);
$data_jurusan_temp 	= [];
foreach ($data_jurusan as $row) {
	$data_jurusan_temp[$row["fakultas_id"]][] = $row;
}
$data_jurusan 	= $data_jurusan_temp;

?>
<form action="edit-profile.php" method="POST" autocomplete="off" enctype="multipart/form-data">

	<div class="container my-2 bg-white py-3 rounded shadow-sm">
		<div class="row">
			<div class="col-12 text-center">
				<h2 class="text-center">Basic Stuff</h2>
			</div>
			<div class="col-12 text-center my-4">
				<img id="profile-pic" src="<?= $data_profile['profile_photo'] ?>" alt="" width="180" height="180" style="border-radius: 50%; object-fit: cover;" title="Click to change">
				<input type="file" id="profile-pic-upload" class="d-none" accept=".png, .jpg, .jpeg" onchange="document.getElementById('profile-pic').src = window.URL.createObjectURL(this.files[0]);" name="profile_photo">
				<input type="text" class="form-control" id="photo-check" hidden>
				<div class="invalid-feedback">Please upload a photo</div>   
			</div>
			<div class="col-md-6 col-12">
				<div class="form-group">
					<label for="name">Full Name*</label>
					<input type="text" id="name" class="form-control" placeholder="First name" name="name" value="<?= $data_profile['name'] ?>" required />
				</div>
			</div>
			<div class="col-md-3 col-sm-6 col-12">
				<div class="form-group">
					<label for="tgl_lahir">Tanggal Lahir*</label>
					<input type="date" id="tgl_lahir" class="form-control" name="tgl_lahir" value="<?= $data_profile["birth_date"] ?>" required />
				</div>
			</div>
			<div class="col-md-3 col-sm-6 col-12">
				<div class="form-group">
					<label for="gender">Jenis Kelamin*</label>
					<select class="form-control" id="gender" name="gender" required="">
						<option <?php if($data_profile['gender'] != "M" && $data_profile['gender'] != "F") {  ?> selected <?php } ?>>Select your gender</option>
						<option value="M" <?php if($data_profile['gender'] == "M") {  ?> selected <?php } ?>>Laki-laki</option>
						<option value="F" <?php if($data_profile['gender'] == "F") {  ?> selected <?php } ?>>Perempuan</option>
					</select>
				</div>
			</div>
			<div class="col-md-3 col-sm-6 col-12">
				<div class="form-group">
					<label for="height">Tinggi</label>
					<input type="text" class="form-control" placeholder="Eg. 170cms or 5'5ft" name="height" value="<?= $data_profile['height'] ?>">
				</div>
			</div>
			<div class="col-md-3 col-sm-6 col-12">
				<div class="form-group">
					<label for="weight">Berat</label>
					<input type="text" class="form-control" placeholder="Eg. 70kgs" name="weight" value="<?= $data_profile['weight'] ?>">
				</div>
			</div>
			<div class="col-md-3 col-sm-6 col-12">
				<div class="form-group">
					<label for="asal_provinsi">Asal Provinsi</label>
					<select name="asal_provinsi" id="asal_provinsi" class="form-control">
						<option value="">Pilih</option>
						<?php foreach ($data_province as $row): ?>
							<option value="<?= $row["id"] ?>" <?= $row["id"] == $data_profile["province_id"] ? "selected" : ""  ?>><?= $row["name"] ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
			<div class="col-md-3 col-sm-6 col-12">
				<div class="form-group">
					<label for="asal_kota">Asal Kota</label>
					<select name="asal_kota" id="asal_kota" class="form-control">
						<option value="">Pilih</option>
					</select>
				</div>
			</div>
			<div class="col-md-6 col-12">
				<div class="form-group">
					<label for="bio">Bio</label>
					<textarea class="form-control" id="bio" rows="7" placeholder="..." name="bio"><?= $data_profile['bio'] ?></textarea>
				</div>
			</div>
			<div class="col-md-6 col-12">
				<div class="form-group">
					<label for="alamat">Alamat</label>
					<textarea class="form-control" id="alamat" rows="3" placeholder="Make it interesting....&#10;Your goal is to impress....." name="alamat"><?= $data_profile['bio'] ?></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="container my-2 bg-white py-3 rounded shadow-sm">
		<div class="row">
			<div class="col-12 text-center mb-3">
				<h2 class="text-center">Socials</h2>
				<p class="text-center">Let them connect on social media!</p>
			</div>
			<div class="col-md-3 col-sm-6 col-12">
				<div class="form-group">
					<label for="ig">Instagram</label>
					<input type="text" class="form-control" name="ig" value="<?= $data_social['ig'] ?>">
				</div>
			</div>
			<div class="col-md-3 col-sm-6 col-12">
				<div class="form-group">
					<label for="twit">Twitter</label>
					<input type="text" class="form-control" name="twit" value="<?= $data_social['twit'] ?>">
				</div>
			</div>
			<div class="col-md-3 col-sm-6 col-12">
				<div class="form-group">
					<label for="tiktok">Tiktok</label>
					<input type="text" class="form-control" name="tiktok" value="<?= $data_social['tiktok'] ?>">
				</div>
			</div>
			<div class="col-md-3 col-sm-6 col-12">
				<div class="form-group">
					<label for="fb">Facebook</label>
					<input type="text" class="form-control" name="fb" value="<?= $data_social['fb'] ?>">
				</div>
			</div>
		</div>
	</div>

	<div class="container my-2 bg-white py-3 rounded shadow-sm">
		<div class="row">
			<div class="col-12 text-center mb-3">
				<h2 class="text-center">Education</h2>
			</div>
			<div class="col-md-6 col-sm-12">
				<div class="form-group">
					<label for="fakultas">Fakultas*</label>
					<select name="fakultas" id="fakultas" class="form-control" required="">
						<option value="">Pilih</option>
						<?php foreach ($data_fakultas as $row): ?>
							<option value="<?php echo $row["id"] ?>" <?= $row["id"] == $data_profile["fakultas_id"] ? "selected" : ""  ?>><?php echo $row["name"] ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
			<div class="col-md-6 col-sm-12">
				<div class="form-group">
					<label for="jurusan">Jurusan*</label>
					<select name="jurusan" id="jurusan" class="form-control" required="">
						<option value="">Pilih</option>
					</select>
				</div>
			</div>
			<div class="col-md-6 col-sm-12">
				<div class="form-group">
					<label for="tahun_masuk">Tahun Akademik*</label>
					<select name="tahun_masuk" id="tahun_masuk" class="form-control" required="">
						<option value="">Pilih Tahun</option>
						<?php for($i = date("Y");$i >= date("Y") - 7;$i--):?>
						<option value="<?= $i ?>" <?= $i == $data_profile["tahun_masuk"] ? "selected" : ""  ?>><?= $i ?></option>
						<?php endfor ?>
					</select>
				</div>
			</div>
			<div class="col-md-6 col-sm-12">
				<div class="form-group">
					<label for="organisasi">Organisasi</label>
					<input type="text" class="form-control" name="organisasi" id="organisasi" placeholder="Organisasi yang diikuti" value="<?php echo $data_profile["organisasi"] ?>">
				</div>
			</div>
		</div>
	</div>

	<div class="container my-2 bg-white py-3 rounded shadow-sm">
		<input type="submit" class="btn btn-sm btn-block p-2 btn-primary submit-btn" name="profile-edit-submit" value="Submit">
	</div>

</form>

<script>
	$(() => {
		$("body").on("change","#asal_provinsi",function(){
			let data_kota 	= JSON.parse(`<?= json_encode($data_kota) ?>`);
			let provinsi 	= $(this).val();
			if (provinsi == "") {
				return 0;
			}
			$("#asal_kota").html("");
			$("#asal_kota").append($("<option>",{"value" : ""}).html("Pilih"));
			$.each(data_kota[provinsi],function(i,row){
				$("#asal_kota").append($("<option>",{"value" : row.id}).html(row.name));
			})
		})

		$("#asal_provinsi").change();
		<?php if(!empty($data_profile["regencies_id"])): ?>
		$("#asal_kota").val("<?= $data_profile["regencies_id"] ?>").select();
		<?php endif ?>

		$("body").on("change","#fakultas",function(){
			let data_jurusan 	= JSON.parse('<?= json_encode($data_jurusan) ?>');
			let fakultas 	= $(this).val();
			if (fakultas == "") {
				return 0;
			}
			$("#jurusan").html("");
			$("#jurusan").append($("<option>",{"value" : ""}).html("Pilih"));
			$.each(data_jurusan[fakultas],function(i,row){
				$("#jurusan").append($("<option>",{"value" : row.id}).html(row.name));
			})
		})
		$("#fakultas").change();
		<?php if(!empty($data_profile["jurusan_id"])): ?>
		$("#jurusan").val("<?= $data_profile["jurusan_id"] ?>").select();
		<?php endif ?>

	})
</script>

</body>
</html>