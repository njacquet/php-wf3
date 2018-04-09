<?php 

/*
lister les commandes dans un tableau HTML:
- id de la commande
- nom prénom de l'utilisateur qui a passé la commande
- montant formaté
- la date de la commande formaté (function date() et strtotime() de PHP);
- statut 
- date de la statut formaté (function date() et strtotime() de PHP);

Passer le statut en liste déroulante (en cours, envoyé, livré) avec un bouton Modifier pour changer le statut de la commande => traiter le changement de statut en mettant à jour statut et date_statut dans la table commande

*/

require_once __DIR__ . '/../include/init.php';
adminSecurity();
// dans le cas de changement de statut on récupère le nouveau statut pour mettre dans la BDD
if(isset($_POST['modifier-statut'])){
var_dump($_POST);
	$query = 'UPDATE commande SET'
	. ' statut = :statut,' . ' date_statut = now()' . ' WHERE id= :id'
	;
	$stmt = $pdo->prepare($query);
	$stmt ->bindValue (':statut', $_POST['statut']);
	$stmt ->bindValue (':id', $_POST['commande-id']);
	$stmt->execute();

	setFlashmessage('Le statut est modifié!');
}


$query = "SELECT c.*, CONCAT_WS(' ', u.prenom, u.nom) AS affichage_id FROM commande c JOIN utilisateur u ON c.utilisateur_id = u.id";
//une autre option de la meme requete avec concatenation . :

// $query = 'SELECT c.*, CONCAT_WS('', u.prenom, u.nom) AS affichage_id' . 'FROM commande c' . 'JOIN utilisateur u ON c.utilisateur_id = u.id'

$stmt = $pdo->query($query); //faire la requete;
$listCommandes = $stmt->fetchAll(); //afficher les résultats de la requete
$statuts = ['en cours', 'envoyer', 'livré'];


include __DIR__ . '/../layouts/top.php';

?>

<h1>Gestion de commandes</h1>

<!-- la tableau HTML avec la liste de commandes passées-->
 <table class="table table-bordered">
		
	<thead class="thead-light">
		<tr>
			<th scope="col">Id_commande</th>
			<th scope="col">Utilisateur</th>
			<th width="250px">Montant total</th>
			<th width="250px">Date de la commande</th>	
			<th width="250px">Statut</th>	
			<th width="250px">Date de statut</th>	
		</tr>	
	</thead>

<?php 
	foreach($listCommandes as $commande){
?>
	<tr>
		<td><?=$commande['id']; ?></td>
		<td><?=$commande['affichage_id']; ?></td>
		<td><?=prixFr($commande['montant_total']); ?></td>
		<td><?=dateFr($commande['date_commande']); ?></td>
		<td>
			<form method="post" class="form-inline">
				<select name="statut" class="form-control">
					<?php 	
						foreach($statuts as $statut) :
							$selected = ($statut== $commande['statut'])
								?'selected'
								: ''
							;

					 ?>
			  				<option value="<?= $statut; ?>" <?= $selected; ?>>
	 							<?=ucfirst($statut); ?>
							</option>
					<?php 	
						endforeach;
					 ?>
				</select>
				<input type="hidden" value="<?=$commande['id'];?>" name="commande-id">	
				<button class="btn btn-primary" type="submit" name="modifier-statut">Modifier</button>
			</form>
		</td>
		<td><?=dateFr($commande['date_statut']); ?></td>
	</tr>

<?php 
	}

 ?>


</table>

 <?php 
include __DIR__ . '/../layouts/bottom.php';

  ?>