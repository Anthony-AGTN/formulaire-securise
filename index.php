<?php
//------------------------------------------------------------------------------------------
// Gestion de la base de données avec PDO --------------------------------------------------
//------------------------------------------------------------------------------------------

// Appel du fichier 'connec.php', contenant les variables DSN, USER et PASS
require_once './connec.php';

// Connexion à la base de données avec PDO
$pdo = new PDO(DSN, USER, PASS);

// Requète pour récupérer tous les enregistrements de la table 'datas_form'
$query = "SELECT * FROM datas_form";
$statement = $pdo->query($query);
$savedFormDatas = $statement->fetchAll();

//------------------------------------------------------------------------------------------
// Traitement des données du formulaire ----------------------------------------------------
//------------------------------------------------------------------------------------------

// Captation du formulaire si un POST est détecté
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    //------------------------------------------------------------------------------------------
    // Traitement des données de $_POST --------------------------------------------------------
    //------------------------------------------------------------------------------------------

    // Suppression des fichiers chargés si POST delete, sinon traitement du formulaire ---------
    if (!empty($_POST['delete'])) {
        array_map('unlink', glob("public/uploads/*"));
    } else {

        // Suppression des espaces blancs des éléments du tableau
        $datas = array_map('trim', $_POST);

        // Création d'un tableau pour stocker les messages d'erreurs
        $errors = [];

        //------------------------------------------------------------------------------------------
        // Validation des données de $_POST --------------------------------------------------------
        //------------------------------------------------------------------------------------------

        // Civilité (Obligatoire + contenu)
        $civilityAccepted = ['Non-binaire', 'Femme', 'Homme'];
        if (empty($datas['civility'])) { // Vérification que le champ soit rempli car il est obligatoire
            $errors[] = 'La civilité est obligatoire !';
        } elseif (!in_array($datas['civility'], $civilityAccepted)) { // Vérification si le champ contient une donnée contenue dans un tableau prédéfini
            $errors[] = 'La civilité n\'est pas valide !';
        }

        // Prénom (Obligatoire + 50 caractères max)
        if (empty($datas['first-name'])) { // Vérification que le champ soit rempli car il est obligatoire
            $errors[] = 'Le prénom est obligatoire !';
        } elseif (strlen($datas['first-name']) > 50) { // Vérification que le champ ne dépasse pas 50 caractères
            $errors[] = 'Votre prénom ne doit pas excéder 50 caractères !';
        }

        // Nom (Obligatoire + 50 caractères max)
        if (empty($datas['last-name'])) { // Vérification que le champ soit rempli car il est obligatoire
            $errors[] = 'Le nom est obligatoire !';
        } elseif (strlen($datas['last-name']) > 50) { // Vérification que le champ ne dépasse pas 50 caractères
            $errors[] = 'Votre nom ne doit pas excéder 50 caractères !';
        }

        // Date de naissance (Obligatoire)
        if (empty($datas['date-of-birth'])) { // Vérification que le champ soit rempli car il est obligatoire
            $errors[] = 'La date de naissance est obligatoire !';
        }

        // Email (Obligatoire + 50 caractères max + filter_var email)
        if (empty($datas['email'])) { // Vérification que le champ soit rempli car il est obligatoire
            $errors[] = 'L\'email est obligatoire !';
        } elseif (strlen($datas['email']) > 50) { // Vérification que le champ ne dépasse pas 50 caractères
            $errors[] = 'Votre email ne doit pas excéder 50 caractères !';
        } elseif (!filter_var($datas['email'], FILTER_VALIDATE_EMAIL)) { // Vérification du champ avec la fonction filter_var
            $errors[] = 'L\'email n\'est pas valide !';
        }

        // Numéro de téléphone (20 caractères max)
        if (isset($datas['telephone'])) { // Vérification si le champ non obligatoire est rempli
            if (strlen($datas['telephone']) > 20) { // Vérification que le champ ne dépasse pas 20 caractères
                $errors[] = 'Votre numéro de téléphone ne doit pas excéder 20 caractères !';
            }
        }

        // Mot de passe (Obligatoire + 50 caractères max)
        if (empty($datas['password'])) { // Vérification que le champ soit rempli car il est obligatoire
            $errors[] = 'Le mot de passe est obligatoire !';
        } elseif (strlen($datas['password']) > 50) { // Vérification que le champ ne dépasse pas 50 caractères
            $errors[] = 'Votre mot de passe ne doit pas excéder 50 caractères !';
        }

        // Adresse (Obligatoire + 50 caractères max)
        if (empty($datas['adress'])) { // Vérification que le champ soit rempli car il est obligatoire
            $errors[] = 'L\'adresse est obligatoire !';
        } elseif (strlen($datas['adress']) > 50) { // Vérification que le champ ne dépasse pas 50 caractères
            $errors[] = 'Votre adresse ne doit pas excéder 50 caractères !';
        }

        // Complément d'adresse (50 caractères max)
        if (isset($datas['additional-address'])) { // Vérification si le champ non obligatoire est rempli
            if (strlen($datas['additional-address']) > 50) { // Vérification que le champ ne dépasse pas 50 caractères
                $errors[] = 'Votre complément d\'adresse ne doit pas excéder 50 caractères !';
            }
        }

        // Code postal (Obligatoire + 50 caractères max + filter_var regexp)
        if (empty($datas['zip-code'])) { // Vérification que le champ soit rempli car il est obligatoire
            $errors[] = 'Le code postal est obligatoire !';
        } elseif (strlen($datas['zip-code']) > 5) { // Vérification que le champ ne dépasse pas 5 caractères
            $errors[] = 'Votre code postal ne doit pas excéder 5 caractères !';
        } elseif (!filter_var($datas['zip-code'], FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/[0-9]{5}/")))) { // Vérification du champ avec la fonction filter_var
            $errors[] = 'Le code postal n\'est pas valide !';
        }

        // Ville (Obligatoire + 50 caractères max)
        if (empty($datas['city'])) { // Vérification que le champ soit rempli car il est obligatoire
            $errors[] = 'La ville est obligatoire !';
        } elseif (strlen($datas['city']) > 50) { // Vérification que le champ ne dépasse pas 50 caractères
            $errors[] = 'Votre ville ne doit pas excéder 50 caractères !';
        }

        // Pays (Obligatoire + contenu)
        $countryAccepted = ['france', 'allemagne', 'angleterre', 'portugal', 'suisse', 'italie'];
        if (empty($datas['country'])) { // Vérification que le champ soit rempli car il est obligatoire
            $errors[] = 'Le pays est obligatoire !';
        } elseif (!in_array($datas['country'], $countryAccepted)) { // Vérification si le champ contient une donnée contenue dans un tableau prédéfini
            $errors[] = 'Le pays ne fait pas partie des pays autorisés !';
        }

        //------------------------------------------------------------------------------------------
        // Traitement des données de $_FILES (Image de profil) -------------------------------------
        //------------------------------------------------------------------------------------------

        if (!empty($_FILES['profile-picture']['name'])) {

            // Chemin vers un dossier sur le serveur qui va recevoir les fichiers uploadés (attention ce dossier doit être accessible en écriture)
            $uploadDir = 'public/uploads/';

            // Le nom de fichier sur le serveur est ici généré à partir du nom de fichier sur le poste du client (mais d'autre stratégies de nommage sont possibles)
            $uploadFile = $uploadDir . basename($_FILES['profile-picture']['name']);

            // Je récupère l'extension du fichier
            $extension = pathinfo($_FILES['profile-picture']['name'], PATHINFO_EXTENSION);

            // Les extensions autorisées (jpg, png, gif, webp)
            $authorizedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            // Je récupère le type mime du fichier
            $typeMime = mime_content_type($_FILES['profile-picture']['tmp_name']);

            // Les types mime autorisées (image/jpeg, png, gif, image/webp)
            $authorizedTypeMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            // Le poids max géré par PHP par défaut est de 2M, dans notre cas, nous sommes à 1M
            $maxFileSize = 1000000;

            // Vérification si l'extension est autorisée
            if ((!in_array($extension, $authorizedExtensions))) {
                $errors[] = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png !';
            }

            // Vérification si le type mime est autorisé
            if ((!in_array($typeMime, $authorizedTypeMime))) {
                $errors[] = 'Le fichier est de type "' . $typeMime . '", veuillez sélectionner une image de type Jpg ou Jpeg ou Png !';
            }

            // On vérifie si l'image existe et si le poids est autorisé en octets
            if (file_exists($_FILES['profile-picture']['tmp_name']) && filesize($_FILES['profile-picture']['tmp_name']) > $maxFileSize) {
                $errors[] = "Votre fichier doit faire moins de 1M !";
            }

            if (empty($errors)) { // Si aucune erreur

                // On ajoute un uniqid au nom de l'image
                $explodeName = explode('.', basename($_FILES['profile-picture']['name']));
                $name = $explodeName[0];
                $extension = $explodeName[1];
                $uniqName = $name . uniqid('', true) . "." . $extension;
                $uploadFile = $uploadDir . $uniqName;

                // On déplace le fichier temporaire vers le nouvel emplacement sur le serveur. Ça y est, le fichier est uploadé
                move_uploaded_file($_FILES['profile-picture']['tmp_name'], $uploadFile);

                // Enregistrement du chemin d'accès à l'image dans la variable $profilePicture
                $profilePicture = $uploadFile;
            }
        }

        if (empty($errors)) { // Si aucune erreur, enregistrement des données du formulaire en base de données

            // Requète pour enregister dans la table 'datas_form' les données du formulaire
            $query = 'INSERT INTO datas_form
                        (`civility`, `first-name`, `last-name`, `date-of-birth`, `email`, `telephone`, `password`, `adress`, `additional-address`, `zip-code`, `city`, `country`)
                        VALUES
                        (:civility, :first_name, :last_name, :date_of_birth, :email, :telephone, :password, :adress, :additional_address, :zip_code, :city, :country)';

            $statement = $pdo->prepare($query);

            $statement->bindValue(':civility', $datas['civility'], \PDO::PARAM_STR);
            $statement->bindValue(':first_name', $datas['first-name'], \PDO::PARAM_STR);
            $statement->bindValue(':last_name', $datas['last-name'], \PDO::PARAM_STR);
            $statement->bindValue(':date_of_birth', $datas['date-of-birth'], \PDO::PARAM_STR);
            $statement->bindValue(':email', $datas['email'], \PDO::PARAM_STR);
            $statement->bindValue(':telephone', $datas['telephone'], \PDO::PARAM_STR);
            $statement->bindValue(':password', $datas['password'], \PDO::PARAM_STR);
            $statement->bindValue(':adress', $datas['adress'], \PDO::PARAM_STR);
            $statement->bindValue(':additional_address', $datas['additional-address'], \PDO::PARAM_STR);
            $statement->bindValue(':zip_code', $datas['zip-code'], \PDO::PARAM_STR);
            $statement->bindValue(':city', $datas['city'], \PDO::PARAM_STR);
            $statement->bindValue(':country', $datas['country'], \PDO::PARAM_STR);

            $statement->execute();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire sécurisé</title>
    <style>
        body {
            background-color: bisque;
        }

        h1 {
            text-align: center;
        }

        h2 {
            margin-top: 0;
            margin-bottom: 0.5em;
        }

        .h2-comment {
            margin-top: 0;
            margin-bottom: 2em;
        }

        img {
            max-width: 400px;
        }

        .main-container {
            margin: auto;
            width: 80vw;
            max-width: 500px;
        }

        .form-container {
            margin-bottom: 2em;
            padding: 2em;
            border: thick double #32a1ce;
            border-radius: 30px;
        }

        .form-input {
            margin-bottom: 2em;
        }

        h3,
        span {
            color: red;
        }

        table,
        td {
            border: 1px solid #333;
        }
    </style>
</head>

<body>

    <div class="main-container">

        <h1>Formulaire sécurisé</h1>

        <!-- Affichage du formulaire HTML + PHP -->
        <div class="form-container">

            <h2>Formulaire HTML + PHP</h2>

            <p class="h2-comment">Formulaire avec validations côté HTML + validations côté PHP</p>

            <form action="" method="post" enctype="multipart/form-data">

                <p>Civilité<span>*</span> :</p>
                <div class="form-input">
                    <input type="radio" id="non-binary" name="civility" value="Non-binaire">
                    <label for="Non-binary">Non-binaire</label>

                    <input type="radio" id="woman" name="civility" value="Femme">
                    <label for="woman">Femme</label>

                    <input type="radio" id="man" name="civility" value="Homme">
                    <label for="man">Homme</label>
                </div>

                <div class="form-input">
                    <label for="first-name">Prénom<span>*</span> : </label>
                    <input type="text" name="first-name" id="first-name" placeholder="Prénom" maxlength="50">
                </div>

                <div class="form-input">
                    <label for="last-name">Nom<span>*</span> : </label>
                    <input type="text" name="last-name" id="last-name" placeholder="Nom" maxlength="50">
                </div>

                <div class="form-input">
                    <label for="date-of-birth">Date de naissance<span>*</span> : </label>
                    <input type="date" name="date-of-birth" id="date-of-birth" min='1900-01-01' max='2022-11-12'>
                </div>

                <div class="form-input">
                    <label for="email">Email<span>*</span> : </label>
                    <input type="email" name="email" id="email" placeholder="anthony@domaine.com">
                </div>

                <div class="form-input">
                    <label for="telephone">Téléphone : </label>
                    <input type="tel" name="telephone" id="telephone" placeholder="Téléphone" maxlength="20" pattern="^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$" title="La valeur saisie n'est pas un numéro français valide !!!">
                </div>

                <div class="form-input">
                    <label for="password">Mot de passe<span>*</span> : </label>
                    <input type="password" name="password" id="password" placeholder="**********" maxlength="50">
                </div>

                <div class="form-input">
                    <label for="adress">Adresse<span>*</span> : </label>
                    <input type="text" name="adress" id="adress" placeholder="N° et libellé de rue" maxlength="50">
                </div>

                <div class="form-input">
                    <label for="additional-address">Complément d'adresse : </label>
                    <input type="text" name="additional-address" id="additional-address" placeholder="N°bât, étage, appt, digicode..." maxlength="50">
                </div>

                <div class="form-input">
                    <label for="zip-code">Code postal<span>*</span> : </label>
                    <input type="text" name="zip-code" id="zip-code" placeholder="Code postal" maxlength="5" pattern="[0-9]{5}" title="La valeur saisie n'est pas un code postal valide !!!">
                </div>

                <div class="form-input">
                    <label for="city">Ville<span>*</span> : </label>
                    <input type="text" name="city" id="city" placeholder="Ville" maxlength="50">
                </div>

                <div class="form-input">
                    <label for="country-select">Pays<span>*</span> :</label>

                    <select name="country" id="country-select">
                        <option value="">--Choisis un pays--</option>
                        <option value="france">France</option>
                        <option value="allemagne">Allemagne</option>
                        <option value="angleterre">Angleterre</option>
                        <option value="portugal">Portugal</option>
                        <option value="suisse">Suisse</option>
                        <option value="italie">Italie</option>
                    </select>
                </div>

                <div class="form-input">
                    <label for="profile-picture">Image de profil</label>
                    <input type="file" name="profile-picture" id="profile-picture" />
                </div>

                <div>
                    <input type="submit" value="Submit !">
                </div>

            </form>

        </div>

        <!-- Affichage du résultat du formulaire ou des erreurs -->
        <div class="form-container">

            <h2>Résultat du formulaire HTML + PHP</h2>

            <p class="h2-comment">Résultat du formulaire avec validations côté HTML + validations côté PHP</p>

            <?php

            if (!empty($errors)) { // Si formulaire soumis avec erreurs on affiche les erreurs
                foreach ($errors as $error) {
                    echo '<h3>' . $error . '</h3>';
                }
            } else {
                if (isset($datas)) { // Si formulaire soumis sans erreurs on affiche les données du formulaire
                    foreach ($datas as $key => $value) {
                        echo '<p><strong>' . $key . ' :</strong> ' . $value . '</p>';
                    }
                    if (isset($profilePicture)) {
                        echo '<img src="' . $profilePicture . '">';
                    }
                } else { // S'il n'y a pas eu de formulaire soumis
                    echo '<p>Aucun résultat à afficher pour l\'instant =^_^=</p>';
                }
            }

            ?>

        </div>

        <!-- Affichage des options -->
        <div class="form-container">
            <form action="" method="post">
                <div class="form-input">
                    <button name="delete" value="1">Supprimer les fichiers chargés</button>
                </div>
            </form>
        </div>

        <!-- Affichage des données enregistrées dans la base de données -->
        <div class="form-container">

            <h2>Résultats enregistrés</h2>

            <p class="h2-comment">Affichage des données enregistrées dans la base de données</p>

            <?php

            if (!empty($savedFormDatas)) { // Si données enregistrées dans la base de données on affiche dans un tableau

                echo '<table>
                        <tr>
                            <th>Clé</th>
                            <th>Valeur</th>
                        </tr>';

                foreach ($savedFormDatas as $savedFormData) {
                    foreach ($savedFormData as $key => $value) {
                        if ((filter_var($key, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/[^0-9]/"))))) { // REGEX pour ne pas afficher les $key contenant des chiffres
                            
                            if (null != $key) { // Sécurisation des données renvoyées avec htmlentities()
                                $key = htmlentities($key);
                            }
                        
                            if (null != $value) { // Sécurisation des données renvoyées avec htmlentities()
                                $value = htmlentities($value);
                            }
                              
                            if ('id' === $key) {
                                echo '<tr>
                                        <td><strong>' . $key . '</strong></td>
                                        <td><strong>' . $value . '</strong></td>
                                    </tr>';
                            } else {
                                echo '<tr>
                                        <td>' . $key . '</td>
                                        <td>' . $value . '</td>
                                    </tr>';
                            }
                        }
                    }
                }

                echo '</table>';
            } else { // S'il n'y a pas eu de formulaire soumis
                echo '<p>Aucun résultat enregistré pour l\'instant =^_^=</p>';
            }

            ?>

        </div>

    </div>

</body>

</html>

<script>
    //------------------------------------------------------------------------------------------
    //-- SCRIPT - pour que "date of birth" ne soit pas supérieur à la date du jour -------------
    //------------------------------------------------------------------------------------------

    /* Fonction pour obtenir un nombre à n chiffres avant la virgule
     * Exemple pour "5" le résultat est "05" */
    function formatIntForTwoNumber(dateValue) {
        if (10 > dateValue) {
            return "0" + dateValue;
        }
        return dateValue;
    }

    // Captation de la date du jour formatée pour l'attribut "max" de l'input de type date
    const dateOfToday = new Date();
    const yearOfToday = dateOfToday.getFullYear();
    const monthOfToday = formatIntForTwoNumber(dateOfToday.getMonth() + 1);
    const dayOfToday = formatIntForTwoNumber(dateOfToday.getDate());
    const dateFormatForMaxDateOfBirth = yearOfToday + "-" + monthOfToday + "-" + dayOfToday;

    // Modification de l'attribut "max" dans l'input "date-of-birth" avec la date du jour
    const dateOfBirth = document.getElementById('date-of-birth');
    dateOfBirth.setAttribute('max', dateFormatForMaxDateOfBirth);
</script>