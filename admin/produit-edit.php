<?php 
require_once __DIR__ . '/../include/init.php';
adminSecurity();

/*
faire le formulaire d'édition de produit
- nom : input text - obligatoire 
- description :  textarea - obligatoire
- reference : input text - obligatoire, 50 car max, unique
- prix : input text - obligatoire
- categorie : select - obligatoire

si le formulaire est bien rempli: INSERT en bdd et redirection vers la liste avec message de confirmation
sinon messages d'erreurs et champs pré-remplis avec les valeurs saisies
Adapter la page pour la modification :
- avoir un bouton dans la page de liste qui pointe vers cette page en passant d'id du produit dans l'URL
- si on a un produit dans l'url sans retour de post, faire une requête select pour
    pré-remplir le formulaire
- adapter le traitement pour faire un update au lieu d'un insert si on a un id dans l'url
- adapter la vérification de l'unicité de la référence pour exclure la référence du produit que l'on modifie de la requête

*/

$nom = $description = $reference = $prix = $categorieId = $photoActuelle = '';
$errors = [];

if (!empty($_POST)) { //si on a des données venant du formulaire
	sanitizePost();
	extract($_POST);
	$categorieId  = $_POST['categorie'];
// verifier si tous les champs sont remplis:
	if (empty($_POST['nom'])){
		$errors[] = 'le nom est obligatoire';
	}

	if (empty($_POST['description'])){
		$errors[] = 'la description est obligatoire';
	
	}

	if (empty($_POST['reference'])){
		$errors[] = 'la référence est obligatoire';
	} elseif (strlen($_POST['nom']) > 50){
		$errors[] = 'la référence de doit pas faire plus de 50 caractères';
	} else{
//
			$query = 'SELECT count(*) FROM produit WHERE reference = :reference';
			if (isset($_GET['id'])){

				//en modification, on exclut de la verification le produit
				// que l'on est en train de modifier
				$query .= ' AND id != ' . $_GET['id'];
			}

			$stmt = $pdo->prepare($query);
			$stmt ->bindValue (':reference', $_POST['reference']);
			$stmt->execute();
			$nb = $stmt->fetchColumn();
			if ($nb != 0) {
			$errors[] = "Il existe déjà un produit avec cette référence";
		}
	}
	if (empty($_POST['prix'])){
		$errors[] = 'le prix est obligatoire';
	}

	if (empty($_POST['categorie'])){
		$errors[] = 'la catégorie est obligatoire';
	}
	//si une image a été téléchargée 
	if (!empty($_FILES['photo']['tmp_name'])){
		// $_FILES ['photo']['size'] = le poid du fichier en octets
		if ($_FILES['photo']['size'] > 1000000){
			$errors[] = 'La photo ne doit pas faire plus de 1Mo';
		}
		$allowedMimeTypes = [
			'image/png',
			'image/jpeg',
			'image/gif'
		];
		if (!in_array($_FILES['photo']['type'], $allowedMimeTypes)){
			$errors[] = 'La photo doit être une image GIF, JPG ou PNG';
		}
		
	}

	//si le formulaire est correctement rempli
	if (empty($errors)){
		if (!empty($_FILES['photo']['tmp_name'])){
			$originalName = $_FILES['photo']['name'];
			// on retrouve l'extension du fichier original à partir de son nom
			//(ex: .png pour mon_fichier.png)
			$extension = substr($originalName, strpos($originalName, '.'));
			// le nom qu va avoir le fichier dans le répertoire photo
			$nomPhoto = $_POST['reference'] . $extension;

			//en modification, si le produit avait déja une photo
			//on la supprime
			if(!empty($photoActuelle)){
				unlink(PHOTO_DIR . $photoActuelle);
			}
			// enregistrement du fichier dans le répertoire photo
			move_uploaded_file($_FILES['photo']['tmp_name'], PHOTO_DIR . $nomPhoto);
		}else  {
			$nomPhoto = $photoActuelle;
		}

		if (isset($_GET['id'])) {
			$query = <<<EOS
UPDATE produit SET
			nom = :nom,
			description = :description,
			reference = :reference,
			prix = :prix,
			categorie_id = :categorieId,
			photo = :photo
WHERE id = :id;
EOS;
			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':id', $_GET['id']);
			$stmt->bindValue(':nom', $_POST['nom']);
			$stmt->bindValue(':description', $_POST['description']);
			$stmt->bindValue(':reference', $_POST['reference']);
			$stmt->bindValue(':prix', $_POST['prix']);
			$stmt->bindValue(':categorieId', $_POST['categorie']);
			$stmt->bindValue(':photo', $nomPhoto);
			
			$stmt->execute();

		} else {
			$query = <<<EOS
INSERT INTO produit(
			nom,
			description,
			reference,
			prix,
			categorie_id,
			photo
) VALUES (
			:nom,
			:description,
			:reference,
			:prix,
			:categorieId,
			:photo
)
EOS;
			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':nom', $_POST['nom']);
			$stmt->bindValue(':description', $_POST['description']);
			$stmt->bindValue(':reference', $_POST['reference']);
			$stmt->bindValue(':prix', $_POST['prix']);
			$stmt->bindValue(':categorieId', $_POST['categorie']);
			$stmt->bindValue(':photo', $nomPhoto);
			$stmt->execute();
		}
		//enregistrement d'une message en session
		setFlashMessage ('le produit est enrégistré');
		//redirection vers la page de liste
		header ('Location: produits.php');
		die;
	}
} elseif(isset($_GET['id'])){
	//pré-remplissage du formulaire à partir de la BDD

	$query = 'SELECT * FROM produit WHERE id= ' . $_GET['id'];
	$stmt = $pdo->query($query); //faire la requete;
	$produit = $stmt->fetch();
	extract($produit);
	// au lieu de extract() on pourrait mettre :
	// $nom = $produit['nom'];
	// $description = $produit['description'];
	// $reference = $produit['reference'];
	// $prix = $produit['prix'];
	$categorieId = $produit['categorie_id'];
	$photoActuelle = $produit['photo'];
}
//pour construire le select des catégories:
$query = 'SELECT * FROM categorie';
$stmt = $pdo->query($query); //faire la requete;
$categorieList = $stmt->fetchAll();


include __DIR__ . '/../layouts/top.php';
if (!empty($errors)):
 ?>
	<div class="alert alert-danger">
		<h5 class="alert-heading">le formulaire contient des erreurs</h5>
		<?= implode('<br>', $errors); //implode transforme un tableau en chaine de caractères ?>
	</div>
<?php 
endif;
 ?>
 <h1>Edition produit</h1>
<!-- l'attribut enctype est obligatoire pour un formulaire qui contient un téléchargement de fichier -->
 <form method="post" enctype="multipart/form-data">
 	<div class="form-group">
 		<label>Nom</label>
 		<input type="text" name="nom" class="form-control" value="<?= $nom; ?>" >
 	</div>
 	<div class="form-group">
 		<label>Description</label>
		<textarea name="description" class="form-control" >
		<?= $description ?> 	
		</textarea>
 	</div>
 	<div class="form-group">
 		<label>Référence</label>
 		<input type="text" name="reference" value = "<?=$reference; ?>" class="form-control">
 	</div>
 	<div class="form-group">
 		<label>Prix</label>
 		<input type="text" name="prix" value = "<?=$prix; ?>" class="form-control">
 	</div>
 	<div class="form-group">
 		<label>Catégorie</label>
 		<select name="categorie" class="form-control">
 			<option value=""></option>
		<?php 	
		 	foreach ($categorieList as $categorieItem) :
		 		$selected = ($categorieItem['id'] == $categorieId)
		 			? 'selected'
		 			: ''
		 		;
 		?>
 			<option value="<?= $categorieItem['id']; ?>"
 				<?= $selected; ?> > <?= $categorieItem['nom']; ?>
 			</option> 
 		<?php 	
 		endforeach
 		?>	
 		</select>
 	</div>	
	<div class="form-group">
 		<label>Photo</label>
 		<input type="file" name="photo">
 	</div>
 	<?php 
 	if (!empty($photoActuelle)) :
 		echo '<p>Actuellement :<br><img src="' .PHOTO_WEB . $photoActuelle . '" height="150px"></p>';
 	endif;
 	 ?>
 	<input type="hidden" name="photoActuelle" value="<?= $photoActuelle; ?>">
 	<div class="form-btn-group text-right">
 		<button type="submit" class="btn btn-primary">Enregistrer</button>
 		<a class="btn btn-secondary" href="produits.php">Retour</a>
 	</div>

 </form>

 <?php 
include __DIR__ . '/../layouts/bottom.php';

  ?>