<?php 
require_once __DIR__ . '/../include/init.php';
adminSecurity();

// Lister  les catégories dans un tableau HTML
//le requetage ici

$query = 'SELECT * FROM categorie';
$stmt = $pdo->query($query); //faire la requete;
$categorieList = $stmt->fetchAll(); //afficher les résultats de la requete
include __DIR__ . '/../layouts/top.php';
 ?>

 <h1>Gestion catégories</h1>
<p> 
	<a class="btn btn-outline-info" href="categorie-edit.php">Ajouter une catégorie</a>

</p>
 <!-- le tableau HTML ici -->
 <h2>Résultats de votre recherche</h2>
<table class="table table-bordered">
		
	<thead class="thead-light">
		<tr>
			<th scope="col">Id</th>
			<th scope="col">Nom</th>
			<th width="250px"></th>
	

		</tr>	
	</thead>
	
	<?php 
	// echo '<pre>';
	// var_dump($categorieList); //objet PDOStatement
	// echo '</pre>';
	foreach ($categorieList as $categorieItem) {
	echo '<tr>';
	echo"<td>" . $categorieItem['id'] . "</td>";
	echo"<td>" . $categorieItem['nom'] . "</td>";
	echo'<td><a class="btn btn-info" href="categorie-edit.php?id=' . $categorieItem['id'] . '">Modifier</a>';
	echo'<a class="btn btn-danger" href="categorie-delete.php?id=' . $categorieItem['id'] . '">Supprimer</a></td>';
	 //affiche le nom/prénom a la ligne 
	echo '</tr>';
	}
	?>

</table>

 <?php 
include __DIR__ . '/../layouts/bottom.php';

  ?>
