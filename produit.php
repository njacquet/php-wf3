<?php 
require_once __DIR__ . '/include/init.php';

$query = 'SELECT * FROM produit WHERE id =' . $_GET['id'];
$stmt = $pdo->query($query);
$produitItem = $stmt->fetch();

$src = ( !empty($produitItem['photo']))
		? PHOTO_WEB . $produitItem['photo']
		: PHOTO_DEFAULT
;

if(!empty($_POST)) {
	ajoutPanier($produitItem, $_POST['quantite']);
	setFlashMessage('Le produit est ajouté au panier');
}
// pour vérifier si la panier se rajoute   
//echo '<pre>';
// var_dump($_SESSION);
// echo '</pre>';

include __DIR__ . '/layouts/top.php';
 ?>

 <h1><?= $produitItem['nom']; ?></h1>

 <div class="row">
 	<div  class="col-md-4 text-center">
 		<img src="<?= $src; ?>" height="200px">
 		<p>
 			<?= prixFr($produitItem['prix']); ?>
 		</p>
 		<form method="post" class="form-inline">
 			<label for="">Qté</label>
 			<select name="quantite" class="form-control">
 				<?php 
 				for ($i = 1; $i <= 10; $i++):
 			 	?>	
					<option value="<?= $i; ?>">
						<?= $i; ?>
					</option>
 			 	<?php 
 				endfor
 			 	 ?>
 			</select>
 			<button type="submit" class="btn btn-primary">
 				Ajouter au panier
 			</button>
 		</form>
 	</div>
 	<div class="col-md-8">
	 	<p>
	 		<?= $produitItem['description']; ?>
	 	</p>
 	</div>

 </div>
 
 <?php 

include __DIR__ . '/layouts/bottom.php';
  ?>

