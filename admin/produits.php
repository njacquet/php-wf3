<?php 
//faire la page qui liste les produits dans un tableau HTML
//tous les champs sauf la description
//bonus : 
//afficher le nom de la catégorie au lieu de son id

require_once __DIR__ . '/../include/init.php';
adminSecurity();
// requetage ici
// choisir tous les champs de table produit (alias p) + le champ nom de table categorie (alias categorie_nom)
//
$query = <<<EOS
SELECT p.*, c.nom AS categorie_nom
FROM produit p
JOIN categorie c ON p.categorie_id = c.id
EOS;

$stmt = $pdo->query($query); //faire la requete;
$produitsList = $stmt->fetchAll(); //afficher les résultats de la requete

include __DIR__ . '/../layouts/top.php';

 ?>
<h1>Gestion de produits</h1>
<p> 
	<a class="btn btn-outline-info" href="produit-edit.php">Ajouter un produit</a>

</p>

 <!-- le tableau HTML ici -->
 <h2>Résultats de votre recherche</h2>
<table class="table table-bordered">
		
	<thead class="thead-light">
		<tr>
			<th scope="col">Id</th>
			<th scope="col">Catégorie</th>
			<th scope="col">Nom</th>
			<th scope="col">Référence</th>
			<th scope="col">Prix</th>
			<th width="250px"></th>
		</tr>	
	</thead>

<?php 
	// echo '<pre>';
	// var_dump($categorieList); //objet PDOStatement
	// echo '</pre>';
	foreach ($produitsList as $produitItem) {
	echo '<tr>';
	echo"<td>" . $produitItem['id'] . "</td>";
	echo"<td>" . $produitItem['categorie_nom'] . "</td>";
	echo"<td>" . $produitItem['nom'] . "</td>";
	echo"<td>" . $produitItem['reference'] . "</td>";
	echo"<td>" . $produitItem['prix'] . "</td>";
	echo'<td><a class="btn btn-info" href="produit-edit.php?id=' . $produitItem['id'] . '">Modifier</a>';
	echo'<a class="btn btn-danger" href="produit-delete.php?id=' . $produitItem['id'] . '">Supprimer</a></td>';
	
	echo '</tr>';
	}
?>

</table>

 <?php 
include __DIR__ . '/../layouts/bottom.php';

  ?>
