<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link href="styles/style.css" rel="stylesheet" type="text/css">
        <title>Résultat Odoo</title>

        <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link href="static/plugin/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    </head>
    <body>
        <?php
        $url = "https://formation.vracoop.fr";
        $db = "formation_test";
        $username = "";
        $password = "";

        require_once('ripcord/ripcord.php');


        $common = ripcord::client("$url/xmlrpc/2/common");
        $common->version();
        $models = ripcord::client("$url/xmlrpc/2/object");

        $uid = $common->authenticate($db, $username, $password, array());

        //permet de récupérer les identifiants des produits qui sont à peser avec une balance
        $ids = $models->execute_kw($db, $uid, $password,
                'product.product', 'search', array(
            array(array('to_weight', '=', true))));

        //Stock dans une variable le nombre de résultats retourné 
        $count = $models->execute_kw($db, $uid, $password,
                'product.product', 'search_count',
                array(array(array('to_weight', '=', true))));

        $message = "";
        ?>
        <div>
            <h1> Articles qui sont à peser avec une balance :</h1>
            <?php
            //Boucle sur le nombre de résultats
            for ($i = 0; $i < $count; $i++) {
                //stock dans la variable $affichage les informations d'un article selon son id
                $affichage = $models->execute_kw($db, $uid, $password,
                        'product.product', 'read',
                        array($ids[$i]),
                        array('fields' => array('name', 'type', 'pos_categ_id', 'lst_price', 'standard_price', 'uom_name')));

                //Construction de l'affichage web
                $message .= '<div class="container_box"><p>Nom : ' . $affichage[0]['name'];
                $message .= '<div class="id">' . $affichage[0]['id'] . '</div>'; //L'id est affiché dans le carré orange
                $message .= "<br />Type : " . $affichage[0]['type'];
                $message .= "<br />Catégorie : " . $affichage[0]['pos_categ_id'][1];
                $message .= "<br />Prix de vente : " . $affichage[0]['lst_price'] . "€";
                $message .= "<br />Prix d'achat : " . $affichage[0]['standard_price'] . "€";
                $message .= "<br />Unité de mesure : " . $affichage[0]['uom_name'] . '</p></div>';
                $message .= "<br /><br /><br />";
                //var_dump($affichage);
            }
            //Affichage des informations sur la page web
            echo $message;
            ?>
        </div>
    </body>
</html>
