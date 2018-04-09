<?php 
/*
- si la panier est vide : afficher un message
- sinon afficher un tableau HTML avec chaque produit du panier:
nom de produit, prix unitaire, quantite, prix total pour le produit 
- faire une fonction getTotalPanier() qui calcule le montant total du panier et l'utiliser sous le tableau pour afficher le total
- remplacer l'affichage de la quantité par un formulaire avec 
	-un <input type="number"> pour la quantité,
	-un input hidden pour voir l'id du produit dont on modifie la qté 
	-un bouton submit
faire une fonction modifierQuantitePanier() qui met à jour la quantité pour le produit si la quantité n'est pas 0, et qui supprime le produit du panier sinon,
appeler cette fonction quand un des formulaires est envoyé 
*/

require_once __DIR__ . '/include/init.php';
	//appeler la fonction modifierQuantitePanier()

// SI LA COMMANDE EST VALIDE
if (isset($_POST['commander'])) {
	//enregistrer la commande et ses détails en bdd
	

	//insertion dans la table commande
	$query = <<<EOS
INSERT INTO commande (
	utilisateur_id,
	montant_total
) VALUES (
	:utilisateur_id,
	:montant_total
)
EOS;

	$stmt = $pdo->prepare($query);
	$stmt ->bindValue (':utilisateur_id', $_SESSION['utilisateur']['id']);
	$stmt ->bindValue (':montant_total', getTotalPanier());
	$stmt->execute();
	//recuperation de l'id de la commande que l'on vient d'insérer
	$commandeId = $pdo->lastInsertId();

	$query = <<<EOS
INSERT INTO detail_commande (
	commande_id,
	produit_id,
	prix,
	quantite
) VALUES (
	:commande_id,
	:produit_id,
	:prix,
	:quantite
)
EOS;
	$stmt = $pdo->prepare($query);
	$stmt-> bindValue (':commande_id', $commandeId);

	foreach ($_SESSION['panier'] as $produitId =>$produit) {
		$stmt ->bindValue (':produit_id', $produitId);
		$stmt ->bindValue (':prix', $produit['prix']);
		$stmt ->bindValue (':quantite', $produit['quantite']);
		$stmt->execute();
	}
	// Afficher un message de confirmation
	setFlashMessage('Votre commande est bien pris en compte');
	//on vide le panier
	$_SESSION['panier'] = [];
}
	

if(isset($_POST['modifier-quantite'])){
	modifierQuantitePanier($_POST['produit-id'], $_POST['quantite']);
	setFlashMessage('Le panier est mis à jour!');
}

include __DIR__ . '/layouts/top.php';
?>
	<h2> Votre panier</h1>
<?php 

//si la panier est vide : afficher un message
if(empty($_SESSION['panier'])):
?>
	<div class="alert alert-info">
		Le panier est vide';
	</div>
<?php 
 else :
	// $panier = $_SESSION['panier'];
	// var_dump($panier);
?>
	<table class="table table-bordered">
		<thead class="thead-light">
		<tr>
			<th width="150px">Photo</th>
			<th scope="col">Nom de produit</th>
			<th scope="col">Prix unitaire</th>
			<th scope="col">Quantité</th>
			<th scope="col">Prix total</th>
			
		</tr>	
		</thead>
<?php 
	foreach ($_SESSION['panier'] as $idArticle => $panierArticle):
		$src = (!empty($panierArticle['photo']))
				? PHOTO_WEB . $panierArticle['photo']
				: PHOTO_DEFAULT
				;
?>
		<tr>
			<td>
			<img src="<?= $src; ?>" alt="photo du produit" height = "150px">
			</td>
			<td><?=$panierArticle['nom']; ?></td>
			<td><?=prixFR($panierArticle['prix']); ?></td>
			<td>
				<!-- <form method="post" class="form-inline" > -->
				<form method="post">
					<input type="number" min="0" value="<?=$panierArticle['quantite'];?>" name="quantite">
					<input type="hidden" value="<?=$idArticle ;?>" name="produit-id">
					<button class="btn btn-primary" type="submit" name="modifier-quantite">Modifier</button>
				</form>
			</td>
			<td><?=prixFR($panierArticle['prix']*$panierArticle['quantite']); ?></td>
		</tr>
		
<?php 
	
	endforeach;

?>
		<tr>
			<th colspan="4"> Total</th>
			<td><?= prixFr(getTotalPanier()); ?></td>
		</tr>
	
	</table>

<?php 
	if(isUserConnected()):
?>
		<form method="post">
			<p class="text-right">
				<button type="submit" name="commander" class="btn btn-primary">
					Valider la commande
				</button>
			</p>
		</form>

 <?php 
 	else:
 ?>
	  	<div class="alert alert-info">
			Vous devez vous connecter ou vous inscrire pour valider la commande
		</div>
<?php 
	endif;
endif;
 ?>

 <?php 
 include __DIR__ . '/layouts/bottom.php';
 