<?php

//------------------------------------------------------------------------------------------
// Traitement des données du formulaire ----------------------------------------------------
//------------------------------------------------------------------------------------------

// Captation du formulaire si un POST est détecté
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    //------------------------------------------------------------------------------------------
    // Traitement des données de $_POST --------------------------------------------------------
    //------------------------------------------------------------------------------------------

    // Suppression des espaces blancs des éléments du tableau
    $datas = array_map('trim', $_POST);

    // Création d'un tableau pour stocker les messages d'erreurs
    $errors = [];

    if (!empty($datas['first-name'])) {
        $firstName = $datas['first-name'];
    }

    if (!empty($datas['last-name'])) {
        $lastName = $datas['last-name'];
    }

    if (!empty($datas['date-of-birth'])) {
        $dateOfBirth = $datas['date-of-birth'];
    }

    if (!empty($datas['email'])) {
        $email = $datas['email'];
    }

    if (!empty($datas['telephone'])) {
        $telephone = $datas['telephone'];
    }

    if (!empty($datas['password'])) {
        $password = $datas['password'];
    }

    if (!empty($datas['adress'])) {
        $adress = $datas['adress'];
    }

    if (!empty($datas['additional-address'])) {
        $additionalAddress = $datas['additional-address'];
    }

    if (!empty($datas['zip-code'])) {
        $zipCode = $datas['zip-code'];
    }

    if (!empty($datas['city'])) {
        $city = $datas['city'];
    }

    if (!empty($datas['country'])) {
        $country = $datas['country'];
    }



    //------------------------------------------------------------------------------------------
    // Traitement des données de $_FILES (Image de profil) -------------------------------------
    //------------------------------------------------------------------------------------------
    if (!empty($_FILES['avatar']['name'])) {
        // chemin vers un dossier sur le serveur qui va recevoir les fichiers uploadés (attention ce dossier doit être accessible en écriture)
        $uploadDir = 'public/uploads/';
        // le nom de fichier sur le serveur est ici généré à partir du nom de fichier sur le poste du client (mais d'autre stratégies de nommage sont possibles)
        $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);
        // Je récupère l'extension du fichier
        $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        // Les extensions autorisées (jpg, png, gif, webp)
        $authorizedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        // Je récupère le type mime du fichier
        $typeMime = mime_content_type($_FILES['avatar']['tmp_name']);
        // Les types mime autorisées (image/jpeg, png, gif, image/webp)
        $authorizedTypeMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        // Le poids max géré par PHP par défaut est de 2M, dans notre cas, nous sommes à 1M
        $maxFileSize = 1000000;

        /****** Si l'extension est autorisée *************/
        if ((!in_array($extension, $authorizedExtensions))) {
            $errors[] = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png !';
        }

        /****** Si le type mime est autorisée *************/
        if ((!in_array($typeMime, $authorizedTypeMime))) {
            $errors[] = 'Le fichier est de type "' . $typeMime . '", veuillez sélectionner une image de type Jpg ou Jpeg ou Png !';
        }

        /****** On vérifie si l'image existe et si le poids est autorisé en octets *************/
        if (file_exists($_FILES['avatar']['tmp_name']) && filesize($_FILES['avatar']['tmp_name']) > $maxFileSize) {
            $errors[] = "Votre fichier doit faire moins de 1M !";
        }

        if (!empty($errors)) {
            $displayErrors = '';
            foreach ($errors as $error) {
                $displayErrors .= "<div><h3>" . $error . "<h3><br></div>";
            }
        } else {

            /****** on ajoute un uniqid au nom de l'image *************/
            $explodeName = explode('.', basename($_FILES['avatar']['name']));
            $name = $explodeName[0];
            $extension = $explodeName[1];
            $uniqName = $name . uniqid('', true) . "." . $extension;
            $uploadFile = $uploadDir . $uniqName;

            /****** Si je n'ai pas d"erreur alors j'upload *************/

            // on déplace le fichier temporaire vers le nouvel emplacement sur le serveur. Ça y est, le fichier est uploadé
            move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile);

            $photo = $uploadFile;
        }
    }

    //------------------------------------------------------------------------------------------
    // Supprimer les fichiers chargés ----------------------------------------------------------
    //------------------------------------------------------------------------------------------
    if (!empty($_POST['delete'])) {
        array_map('unlink', glob("public/uploads/*"));;
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
    </style>
</head>

<body>

    <div class="main-container">

        <h1>Formulaire sécurisé</h1>

        <div class="form-container">

            <h2>Formulaire HTML + PHP</h2>

            <p class="h2-comment">Formulaire avec validations côté HTML + validations côté PHP</p>

            <form action="" method="post" enctype="multipart/form-data">

                <p>Civilité :</p>
                <div class="form-input">
                    <input type="radio" id="non-binary" name="civility" value="Non-binaire">
                    <label for="Non-binary">Non-binaire</label>

                    <input type="radio" id="woman" name="civility" value="Femme">
                    <label for="woman">Femme</label>

                    <input type="radio" id="man" name="civility" value="Homme">
                    <label for="man">Homme</label>
                </div>

                <div class="form-input">
                    <label for="first-name">Prénom : </label>
                    <input type="text" name="first-name" id="first-name" placeholder="Prénom" maxlength="50">
                </div>

                <div class="form-input">
                    <label for="last-name">Nom : </label>
                    <input type="text" name="last-name" id="last-name" placeholder="Nom" maxlength="50">
                </div>

                <div class="form-input">
                    <label for="date-of-birth">Date de naissance : </label>
                    <input type="date" name="date-of-birth" id="date-of-birth" min='1900-01-01' max='2022-11-12'>
                </div>

                <div class="form-input">
                    <label for="email">Email: </label>
                    <input type="email" name="email" id="email" placeholder="anthony@domaine.com">
                </div>

                <div class="form-input">
                    <label for="telephone">Téléphone : </label>
                    <input type="tel" name="telephone" id="telephone" placeholder="Téléphone" maxlength="20" pattern="^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$" title="La valeur saisie n'est pas un numéro français valide !!!">
                </div>

                <div class="form-input">
                    <label for="password">Mot de passe : </label>
                    <input type="password" name="password" id="password" placeholder="**********" maxlength="50">
                </div>

                <div class="form-input">
                    <label for="adress">Adresse : </label>
                    <input type="text" name="adress" id="adress" placeholder="N° et libellé de rue" maxlength="50">
                </div>

                <div class="form-input">
                    <label for="additional-address">Complément d'adresse : </label>
                    <input type="text" name="additional-address" id="additional-address" placeholder="N°bât, étage, appt, digicode..." maxlength="50">
                </div>

                <div class="form-input">
                    <label for="zip-code">Code postal : </label>
                    <input type="text" name="zip-code" id="zip-code" placeholder="Code postal" maxlength="5" pattern="[0-9]{5}" title="La valeur saisie n'est pas un code postal valide !!!">
                </div>

                <div class="form-input">
                    <label for="city">Ville : </label>
                    <input type="text" name="city" id="city" placeholder="Ville" maxlength="50">
                </div>

                <div class="form-input">
                    <label for="country-select">Pays :</label>

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

        <div class="form-container">
            <form action="" method="post">
                <div class="form-input">
                    <button name="delete" value="1">Supprimer les fichiers chargés</button>
                </div>
            </form>
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