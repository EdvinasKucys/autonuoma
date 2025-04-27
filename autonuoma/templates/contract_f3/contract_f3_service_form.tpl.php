<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="index.php">Pradžia</a></li>
		<li class="breadcrumb-item" aria-current="page"><a href="index.php?module=<?php echo $module; ?>&action=list">Sutartys</a></li>
		<li class="breadcrumb-item" aria-current="page"><a href="index.php?module=<?php echo $module; ?>&action=edit&id=<?php echo $contractId; ?>">Sutarties redagavimas</a></li>
		<li class="breadcrumb-item active" aria-current="page"><?php if(!empty($serviceId) && !empty($dateFrom)) echo "Užsakytos paslaugos redagavimas"; else echo "Nauja užsakyta paslauga"; ?></li>
	</ol>
</nav>

<?php if($formErrors != null) { ?>
	<div class="alert alert-danger" role="alert">
		Neįvesti arba neteisingai įvesti šie laukai:
		<?php 
			echo $formErrors;
		?>
	</div>
<?php } ?>

<form action="" method="post" class="d-grid gap-3">
	

	<div class="form-group">
		<label for="pavaru_deze">Pavadinimas<?php echo in_array('paslauga', $required) ? '<span> *</span>' : ''; ?></label>
		<select class="elementSelector form-select form-control" name="paslauga" <?php if(isset($data['editing'])) { ?> readonly="readonly" <?php } ?>>
			<?php
				$allServices = $servicesObj->getServicesList();
				foreach($allServices as $service) {
					echo "<optgroup label='{$service['pavadinimas']}'>";
					$prices = $servicesObj->getServicePrices($service['id']);
					foreach($prices as $price) {
						$selected = "";
						if(isset($data['fk_kaina_galioja_nuo']) ) {
							if($data['fk_kaina_galioja_nuo'] == $price['galioja_nuo'] && $data['fk_paslauga'] == $price['fk_paslauga']) {
								$selected = " selected='selected'";
							}
						}
						echo "<option{$selected} value='{$price['fk_paslauga']}#{$price['galioja_nuo']}'>{$service['pavadinimas']} {$price['kaina']} EUR ( {$price['galioja_nuo']})</option>";
					}
				}
			?>
		</select>
	</div>

	<div class="form-group">
		<label for="kaina">Kaina<?php echo in_array('kaina', $required) ? '<span> *</span>' : ''; ?></label>
		<input type="text" id="kaina" name="kaina" class="form-control" value="<?php echo isset($data['kaina']) ? $data['kaina'] : ''; ?>">
	</div>
	
	<div class="form-group">
		<label for="kiekis">Kiekis<?php echo in_array('kiekis', $required) ? '<span> *</span>' : ''; ?></label>
		<input type="text" id="kiekis" name="kiekis" class="form-control" value="<?php echo isset($data['kiekis']) ? $data['kiekis'] : ''; ?>">
	</div>

	<?php if(isset($data['fk_sutartis'])) { ?>
		<input type="hidden" name="fk_sutartis" value="<?php echo $data['fk_sutartis']; ?>" />
	<?php } ?>

	<p class="required-note">* pažymėtus laukus užpildyti privaloma</p>

	<input type="submit" class="btn btn-primary w-25" name="submit" value="Išsaugoti">
</form>