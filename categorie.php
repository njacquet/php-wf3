<?php 
// -afficher le nom de la catégorie dont on a recu l'id dans l'url en titre de la page
// - lister les produits appartenant à la catégorie avec leur photo s'ils en ont une

require_once __DIR__ . '/include/init.php';
adminSecurity();
//requete pour afficher le nom de la catégorie choisie
$query = 'SELECT * FROM categorie WHERE id =' . $_GET['id'];
$stmt = $pdo->query($query); //faire la requete;
$categorie = $stmt->fetch();//afficher les résultats de la requete



//REQUETE POUR AFFICHER LA LISTE DE PRODUITS DE LA CATEGORIE CHOISIE
$query = 'SELECT * FROM produit WHERE categorie_id =' . $_GET['id'];
$stmt = $pdo->query($query); //faire la requete;
$produitsList = $stmt->fetchAll(); //afficher les résultats de la requete

include __DIR__ . '/layouts/top.php';
 ?>

<h1><?=$categorie['nom'];?></h1>
<table class="table table-bordered">
		
	<thead class="thead-light">
		<tr>
			<th width="150px">Photo</th>
			<th scope="col">Nom</th>
			<th scope="col">Référence</th>
			<th scope="col">Description</th>
			<th scope="col">Prix</th>
			<th scope="col">Détails</th>
			<th></th>
		</tr>	
	</thead>

<?php 
foreach ($produitsList as $produitItem):
?>
	<tr>
		<td>
			<?php 
			// Défenir la valeur de src pour afficher la photo du produit ou la photo par default :
				$src = ( !empty($produitItem['photo']))
				? PHOTO_WEB . $produitItem['photo']
				: PHOTO_DEFAULT

			 ?>
			<img src="<?= $src; ?>" alt="photo du produit" height = "150px">
		</td>
		<td><?=$produitItem['nom'];?></td>
		<td><?=$produitItem['reference'];?></td>
		<td><?=$produitItem['description'];?></td>
		<td><?= prixFr($produitItem['prix']);?></td>
		<td>
			<a class="btn btn-primary" href="produit.php?id= <?= $produitItem['id']; ?>">Voir</a>
		</td>
	</tr>
	
<?php 
endforeach;
 ?>

</table>
<?php 

include __DIR__ . '/layouts/bottom.php';

 ?>
