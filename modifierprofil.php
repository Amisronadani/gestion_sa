<?php
session_start(); // Démarrer la session
require 'connexion.php';
$id_client = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM client WHERE id_client = ?");
$stmt->execute([$id_client]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

// Gestion du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération et trim des champs
    $nom       = trim($_POST['nom'] ?? '');
    $prenom    = trim($_POST['prenom'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $pays      = trim($_POST['pays'] ?? '');
    $province  = trim($_POST['province'] ?? '');
    $commune   = trim($_POST['commune'] ?? '');
    $collines  = trim($_POST['collines'] ?? '');
    $adresse   = trim($_POST['adresse'] ?? '');
    $password  = $_POST['password'] ?? '';

    // Champs obligatoires
    $required_fields = [
        'Nom' => $nom,
        'Prénom' => $prenom,
        'Email' => $email,
        'Mot de passe' => $password,
        'Pays' => $pays,
        'Province' => $province,
        'Commune' => $commune,
        'Collines' => $collines
    ];

    // Vérification des champs vides
    foreach ($required_fields as $field_name => $value) {
        if (empty($value)) {
            $_SESSION['error'] = "Le champ '$field_name' est obligatoire.";
            exit();
        }
    }

    // Si pas d'erreur, vérifier email unique
    if (empty($_SESSION['error'])) {
        $check = $pdo->prepare("SELECT id_client FROM client WHERE email = ? AND id_client != ?");
        $check->execute([$email, $id_client]);
        if ($check->rowCount() > 0) {
            $_SESSION['error'] = "Email déjà utilisé.";
            header("Location: afficheprofil.php");
            exit();
        }
    }

    $photoName = '';
    // Gestion photo seulement si pas d'erreur
    if (empty($_SESSION['error'])) {
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            // Définir le dossier de téléchargement
            $uploadDir = 'uploads/';
            $photoName = basename($_FILES['photo']['name']);
            $uploadFile = $uploadDir . $photoName;

            // Créer le dossier si nécessaire
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Déplacer le fichier téléchargé
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                $_SESSION['error'] = "Erreur lors de l'upload de la photo.";
                header("Location: afficherprofil.php");
                exit();
            }
        }
    }

    // Mise à jour si pas d'erreur
    if (empty($_SESSION['error'])) {
        try {
            $stmt = $pdo->prepare(
                "UPDATE client SET nom = ?, prenom = ?, email = ?, telephone = ?, pays = ?, province = ?, commune = ?, collines = ?, adresse = ?, password = ?, photo = ? WHERE id_client = ?"
            );
            $stmt->execute([
                $nom, $prenom, $email, $telephone, $pays,
                $province, $commune, $collines, $adresse,
                password_hash($password, PASSWORD_DEFAULT), // Hachage du mot de passe
                $photoName, // Enregistre seulement le nom de la photo
                $id_client
            ]);
            $_SESSION['success'] = "Profil mis à jour avec succès.";
            header("Location: afficherprofil.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la mise à jour : " . $e->getMessage();
            header("Location: afficherprofil.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Formulaire Compte Client</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Votre CSS existant ici... */
    form {
      /*display: block*/;
      width: 1200px;
      margin-left: 0px ;
      padding: 30px;
      background: #f9f9f9;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-family: Arial, sans-serif;
      margin-top:0px;
    }

    form h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
      font-size: 22px;
    }

    .form-row {
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-row .form-group {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .form-group label {
      margin-bottom: 5px;
      font-weight: bold;
      color: #555;
      font-size: 14px;
    }

    .form-group input {
      padding: 14px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 18px;
    }
    .form-group select {
      padding: 14px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 18px;
    }

    /* Zone avatar */
    .photo-preview {
      text-align: center;
      margin-bottom: 10px;
      position: relative;
      display: inline-block;
      margin-top: 5px;
      cursor: pointer;-size: 24px;
    }

    .avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 2px solid #ccc;
      object-fit: cover;
    }

    .photo-preview i {
      font-size: 120px;
      color: #aaa;
    }

    .photo-preview img {
      display: none;
    }

    /* Bouton ajouter (+) */
    .add-icon {
      display: inline-block;
      margin-top: 8px;
      font#007bff;
      cursor: pointer;-size: 24px;
      color: 
    }

    .add-icon:hover {
      color: #0056b3;
    }
    .add-icon {
      display: inline-block;
      margin-top: 8px;
      font#007bff;
      cursor: pointer;-size: 24px;
      color: 
    }
    /* cacher input file */
    #photo {
      display: none;
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-top: 15px;
    }

    .form-actions button {
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
    }

    .btn-save {
      background-color: #28a745;
      color: white;
    }

    .btn-cancel {
      background-color: #dc3545;
      color: white;
    }

    .btn-save:hover {
      background-color: #218838;
    }

    .btn-cancel:hover {
      background-color: #c82333;
    }
    /* Autres styles ci-dessus.... */
  </style>
</head>
<body>

<?php
include('index1.php');
?>
<main>
<form method="POST" enctype="multipart/form-data" action="">

    <h2>Créer un Compte Client</h2>

    <!-- Ligne Photo -->
    <div class="form-row">
      <div class="form-group">
        <div class="photo-preview">
          <i id="icon" class="fas fa-user-circle"></i>
          <img id="preview" class="avatar" src="uploads/<?= $client['photo'] ?>" alt="Prévisualisation">
          <div class="add-icon" onclick="document.getElementById('photo').click()">
          <i class="fas fa-plus-circle"></i>
        </div>
        <input type="file" id="photo" name="photo" accept="image/*" onchange="previewImage(event)">
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" placeholder="Entrez le nom" value="<?= $client['nom'] ?>">
      </div>
      <div class="form-group">
        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" placeholder="Entrez le prénom" value="<?= $client['prenom'] ?>">
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Entrez l'email" value="<?= $client['email'] ?>">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="telephone">Téléphone</label>
        <input type="tel" id="telephone" name="telephone" placeholder="Entrez le téléphone" value="<?= $client['telephone'] ?>">
      </div>
      <div class="form-group">
        <label for="pays">Pays</label>
        <select id="pays" name="pays" onchange="updateProvinces()">
          <option value="">Sélectionnez un pays</option>
        </select>
      </div>

      <div class="form-group">
        <label for="province">Province</label>
        <select id="province" name="province" onchange="updateCommunes()" disabled>
          <option value="">Sélectionnez une province</option>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="commune">Commune</label>
        <select id="commune" name="commune" onchange="updateCollines()" disabled>
          <option value="">Sélectionnez une commune</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="collines">Collines</label>
        <select id="collines" name="collines" disabled>
          <option value="">Sélectionnez une colline</option>
        </select>
      </div>
      <div class="form-group">
        <label for="adresse">Adresse Actuelle </label>
        <input type="text" id="adresse" name="adresse" value="<?= $client['adresse'] ?>" placeholder="Entrez l'adresse" required>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="password">Password (Signature)</label>
        <input type="password" id="password" name="password"  placeholder="Entrez le mot de passe" required>
      </div>
      <div class="form-group">
     
    </div>
      <div class="form-actions">
      <button type="submit" class="btn-save">Mettre a jour</button>
      <button type="reset" class="btn-cancel" onclick="resetPreview()">Annuler</button>
    </div>

    </div>


  </form>
</main>

<script>
// Données des provinces et communes uniquement pour le Burundi
const province_data = {
  "Burundi": {
      "Bujumbura": {
        "Bubanza": ["Bubanza", "Buhororo", "Buvyuko", "Ciya", "Gahongore", "Gatura", "Gitanga", "Karinzi", "Kazeke", "Kivyiru", "Mitakataka", "Mugimbu", "Muhanza", "Muhenga", "Muramba", "Mwanda", "Ngara", "Nyabitaka", "Rugunga", "Rurabo", "Shari"],
        "Mukaza": ["Kanyosha", "Kinama", "Kinindo", "Musaga", "Ngagara", "Nyakabiga", "Rohero", "Muha", "Bukinanyana", "Mpanda", "Muhuta"]
      },
      "Gitega": {
        "Bigendana": ["Bugarama", "Burangwa", "Gahuni", "Gitwaro", "Janda", "Kagoma", "Kayombe", "Kizuga", "Magara I", "Magara II", "Magara III", "Magara IV", "Mihongoro", "Gisuru", "Mugendo", "Nyabungere", "Nyagushwe", "Saga"],
        "Gitega": ["Gihanga","Birohe","Bukwazo","Butamuheba","Bwoga","Higiro","Jimbi","Karenana","kibiri","kimanama","Magarama","Mahombo","Mirama","Mubuga","Mugoboka","Mugutu","Mukanda","Mungwa","Murigwe","Mushasha","Musinzira","Ngobeke","Ntobwe","Nyabiharage","Nyabitutsi","Nyabututsi-urbain","Nyakibingo","Nyamugari","Rango","Rweza","Songa","yoba"],
        "Karusi": ["Bugenyuzi", "Bhindye", "Bihemba", "Bonero", "Nyagoba", "Rugazi", "Ruharo", "Rusenga", "Rwandagaro", "Rwimbogo"],
        "Kiganda": ["Muramvya", "Bukeye", "Kiganda", "Mbuye", "Rutegama"],
        "Gishubi": ["Bucana","Bukwavu","cimba","Gatare","Gatoza","Gishubi","Kayogoro","Kigomero","Mujejure","Musenga","Ndago","Remera",],
        "Muramvya": ["Busimba","Gakenke","Gatebe","Gatwaro","Gishubi","Kavya","Kibogoye","Masango","Shombo","Murambi",],
        "Mwaro": ["Bisoro", "Gisozi", "Kayokwe", "Ndava"],
        "Nyabihanga": ["Buhogo","Butegeye","Gatwe","Gihoma","Kavumu","Kibogoye","Kirambi","Kivomwa","Kivuzo","Magamba","Matyazo","Munago","Muyange","Muyebe",],
        "Shombo": ["Bukirasazi","Rutwe","Kiyange","Kiryama","Shombo",]
      },
      "Butanyerera": {
        "Busoni": [],
        "Matongo": [],
        "Kayanza": [],
        "Kiremba": [],
        "Kirundo": [],
        "Muhanga": [],
        "Mwumba": [],
        "Tangara": []
      },
      "Buhumuza": {
        "Butaganzwa": ["Muramba", "Ntobwe", "Nyabucugu", "Nyagishiru"],
        "Cankuzo": ["Cankuzo", "Gisagara", "Kigamba", "Mishiha", "Msipuri"],
        "Muyinga": ["Bugungu", "Buhinyuza", "Bunywana", "Butihinda", "Bwasira", "Gasave", "Gihongo", "Gitaramuka", "Jarama", "Kara", "Karehe", "Karongwe", "Kibimba", "Kiyange", "Mabago", "Nyankurazo", "Nyaruhengeri", "Nyarunazi", "Rugazi", "Rugongo", "Ruvumu"],
        "Kirundo": ["Bugabira", "Gaturanda", "Gitwe", "Kigina", "Kigoma", "Kiri", "Kiyonza", "Nyabikenke", "Nyakarama", "Rubuga", "Rugasa"],
        "Ruyigi": ["Butaganzwa", "Butezi", "Bweru", "Bisuru", "Kinyinya", "Nyabitsinda", "Ruyigi", "Bugama", "Bunyambo", "Buterangira", "Caga", "Gacokwe", "Gihinga", "Gakangaga", "Gisuru", "Itaba", "Itaheiteka", "Kabingo", "Kabuyenge", "Kavumwe", "Kinama", "Kinanira", "Kireka", "Migende", "Rutonde", "Ruhuni", "Rukobe", "Rubanga", "Nyakivumu", "Nyakirunga"]
      },
      "Burunga": {
        "Bururi": ["Buhinga", "Burarana", "Burenza", "Burunga", "Bururi", "Gahago", "Gasenyi", "Gisanze", "Jungwe", "Karwa", "Kiganda", "Kiremba-sud", "Mahonda", "Mubuga", "Mudahandwa", "Mugozi", "Munini", "Murago", "Muzima", "Nyamiyaga", "Nyarugera", "Nyarwaga", "Nyavyamo", "Rukanda", "Rushemeza", "Ruvumu", "Tongwe"],
        "Matana": ["Bihanga", "Bitezi", "Butwe", "Gisarenda", "Gisisye", "Gitanga", "Kinyinya", "Mahango", "Matana", "Mugano", "Ntega", "Ruzira", "Sakinyonga"],
        "Makamba": ["Burima", "Kirinzi", "Kibimbi", "Kigamba", "Mabanda", "Mara", "Mivo", "Mubondo", "Musenyi", "Mutwazi", "Nyabitabo", "Nyamugari", "Ruvuga"],
        "Rutana": ["Bukemba", "Giharo", "Gitanga", "Mpinga-kayove", "Musongati", "Rutana", "Bugiga", "Butare", "Gihofi", "Kabanga", "Murama-rugwe", "Muyombwe", "Ruganga", "Ruranga"],
        "Rumonge": ["Bugahara", "Burambi", "Buyengero", "Muhuta", "Rumonge", "Birimba"]
      }
    }
};

// Liste des pays à afficher
const pay_data = [
  "Afghanistan", "Afrique du Sud", "Albanie", "Algérie", "Allemagne", 
    "Andorre", "Angola", "Antigua-et-Barbuda", "Arabie saoudite", "Argentine", 
    "Arménie", "Australie", "Autriche", "Azerbaïdjan", "Bahamas", 
    "Bahreïn", "Bangladesh", "Barbade", "Belize", "Bénin", 
    "Bhoutan", "Biélorussie", "Birmanie (Myanmar)", "Bolivie", "Bosnie-et-Herzégovine", 
    "Botswana", "Brésil", "Brunei", "Bulgarie", "Burkina Faso", 
    "Burundi", "Cambodge", "Cameroun", "Canada", "Cap-Vert", 
    "Centrafrique", "Chad", "Chili", "Chine", "Chypre", 
    "Colombie", "Comores", "Congo (Brazzaville)", "Congo (Kinshasa)", "Corée du Nord", 
    "Corée du Sud", "Costa Rica", "Côte d’Ivoire", "Croatie", "Curaçao", 
    "Danemark", "Djibouti", "Dominique", "Égypte", "Émirats arabes unis", 
    "Équateur", "Érythrée", "Espagne", "Eswatini", "États-Unis", 
    "Estonie", "Éthiopie", "Fidji", "Finlande", "France", 
    "Gabon", "Gambie", "Géorgie", "Ghana", "Grèce", 
    "Grenade", "Guatemala", "Guinée", "Guinée-Bissau", "Guinée équatoriale", 
    "Haïti", "Honduras", "Hongrie", "Inde", "Indonésie", 
    "Iran", "Irak", "Irlande", "Israël", "Italie", 
    "Jamaïque", "Japon", "Jordanie", "Kazakhstan", "Koweït", 
    "Laos", "Lesotho", "Lettonie", "Liban", "Liberia", 
    "Libye", "Liechtenstein", "Lituanie", "Luxembourg", "Macédoine du Nord", 
    "Madagascar", "Malaisie", "Malawi", "Maldives", "Mali", 
    "Malte", "Maroc", "Maurice", "Mauritanie", "Mexique", 
    "Micronésie", "Moldavie", "Monaco", "Mongolie", "Monténégro", 
    "Mozambique", "Namibie", "Nauru", "Népal", "Nicaragua", 
    "Niger", "Nigéria", "Norvège", "Nouvelle-Zélande", "Oman", 
    "Ouganda", "Ouzbékistan", "Pakistan", "Palaos", "Palestine", 
    "Panama", "Papouasie-Nouvelle-Guinée", "Paraguay", "Pays-Bas", "Pérou", 
    "Philippines", "Pologne", "Portugal", "Qatar", "Roumanie", 
    "Royaume-Uni", "Russie", "Rwanda", "Saint-Kitts-et-Nevis", "Saint-Vincent-et-les-Grenadines", 
    "Sainte-Lucie", "Saint-Marin", "Salomon", "Samoa", "Saint-Siège", 
    "Sénégal", "Serbie", "Seychelles", "Sierra Leone", "Singapour", 
    "Slovaquie", "Slovénie", "Somalie", "Soudan", "Soudan du Sud", 
    "Sri Lanka", "Suède", "Suisse", "Suriname", "Syrie", 
    "Tadjikistan", "Taïwan", "Tanzanie", "Tchad", "Tchéquie", 
    "Thaïlande", "Timor-Leste", "Togo", "Tonga", "Trinité-et-Tobago", 
    "Tunisie", "Turkménistan", "Turquie", "Tuvalu", "Ukraine", 
    "Uruguay", "Vanuatu", "Venezuela", "Viêt Nam", "Yémen", 
    "Zambie", "Zimbabwe"
];

// Initialiser les pays dans le sélecteur lorsqu'on charge la page
window.onload = function() {
    const paysSelect = document.getElementById('pays');
    pay_data.forEach(pays => {
        paysSelect.innerHTML += `<option value="${pays}">${pays}</option>`;
    });

    // Remplir automatiquement les sélecteurs en fonction des données du client
    const selectedCountry = "<?= $client['pays'] ?>"; // Remplace par $client['pays']
    const selectedProvince = "<?= $client['province'] ?>"; // Remplace par $client['province']
    const selectedCommune = "<?= $client['commune'] ?>"; // Remplace par $client['commune']
    const selectedColline = "<?= $client['collines'] ?>"; // Remplace par $client['collines']

    paysSelect.value = selectedCountry;
    updateProvinces();

    // Après la mise à jour des provinces, sélectionner la bonne province
    const provinceSelect = document.getElementById('province');
    provinceSelect.value = selectedProvince;
    updateCommunes();

    // Après la mise à jour des communes, sélectionner la bonne commune
    const communeSelect = document.getElementById('commune');
    communeSelect.value = selectedCommune;
    updateCollines();

    // Après la mise à jour des collines, sélectionner la bonne colline
    const collinesSelect = document.getElementById('collines');
    collinesSelect.value = selectedColline;
};

// Mettre à jour le sélecteur des provinces uniquement pour le Burundi
function updateProvinces() {
    const provinceSelect = document.getElementById('province');
    const paysSelect = document.getElementById('pays');

    provinceSelect.innerHTML = '<option value="">Sélectionnez une province</option>';
    document.getElementById('commune').innerHTML = '<option value="">Sélectionnez une commune</option>';
    document.getElementById('collines').innerHTML = '<option value="">Sélectionnez une colline</option>';
    document.getElementById('commune').disabled = true;
    document.getElementById('collines').disabled = true;

    const selectedPays = paysSelect.value;
    if (selectedPays === "Burundi") {
        for (const province in province_data.Burundi) {
            provinceSelect.innerHTML += `<option value="${province}">${province}</option>`;
        }
        provinceSelect.disabled = false;
    }
}

// Mettre à jour le sélecteur des communes
function updateCommunes() {
    const communeSelect = document.getElementById('commune');
    const provinceSelect = document.getElementById('province');

    communeSelect.innerHTML = '<option value="">Sélectionnez une commune</option>';
    document.getElementById('collines').innerHTML = '<option value="">Sélectionnez une colline</option>';
    document.getElementById('collines').disabled = true;

    const selectedProvince = provinceSelect.value;

    if (province_data.Burundi[selectedProvince]) {
        for (const commune of Object.keys(province_data.Burundi[selectedProvince])) {
            communeSelect.innerHTML += `<option value="${commune}">${commune}</option>`;
        }
        communeSelect.disabled = false;
    }
}

// Mettre à jour le sélecteur des collines
function updateCollines() {
    const collinesSelect = document.getElementById('collines');
    const communeSelect = document.getElementById('commune');
    const provinceSelect = document.getElementById('province');

    collinesSelect.innerHTML = '<option value="">Sélectionnez une colline</option>';

    const selectedCommune = communeSelect.value;
    const selectedProvince = provinceSelect.value;

    if (province_data.Burundi[selectedProvince] && province_data.Burundi[selectedProvince][selectedCommune]) {
        province_data.Burundi[selectedProvince][selectedCommune].forEach(colline => {
            collinesSelect.innerHTML += `<option value="${colline}">${colline}</option>`;
        });
        collinesSelect.disabled = false;
    }
}
// Fonction pour prévisualiser l'image
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('preview');
        const icon = document.getElementById('icon');
        output.src = reader.result;
        output.style.display = "inline-block"; 
        icon.style.display = "none";           
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>




