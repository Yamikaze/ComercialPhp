<?php
session_start();

   include 'gestionPanier.php';
   include 'PDO.php';
 
if(isset($_POST['categorie']))
   {


       $cat = $bdd->prepare('SELECT num_categorie FROM categorie WHERE libelle=:c');
       $cat->bindParam(':c', $_POST['categorie']);
       $cat->execute() or die('Erreur sql1');
       $c = $cat->fetch();
       
       $cmd = $bdd->prepare('SELECT * FROM produit WHERE num_categorie=:c');
       $cmd->bindParam(':c', $c['num_categorie']);
       $cmd->execute()  or die("erreur sql 2");
       
       $i=0;
       $cmp=0;
       
       echo '<tr>';
       while($donnees = $cmd->fetch())
       {
          $tmp = $donnees['photo'];
          $img = deletePattern('1-', $tmp);
          $promo = $donnees['promo'];
          
          if($cmp == 4)
          {
              echo '<tr>';
          }
           echo'<td>
                    <table border="1px;">
               <tr>
                   <th>Numero</th>
                   <td>' . $donnees['num_produit'] . '</td>
               </tr>
               <tr>
                   <th>Nom</th>
                   <td>' . $donnees['nom_produit'] . '</td>
               </tr>
               <tr>
                   <th>Prix</th>
                   <td>' . $donnees['prix'] .'</td>
               </tr>
               <tr>
                   <th>Image</th>';
           if($promo) {
            list($width, $height) = getimagesize($tmp);


            $ressource = imagecreatefromjpeg($tmp);
            imagecopyresampled($ressource, $ressource, 0, 0, 0, 0, $width, $height, $width, $height) or die('ERROR : resampled');
            imagestring($ressource, 4, 0, 0, 'PROMO !!', imagecolorallocate($ressource, 0, 0, 0));
            imagejpeg($ressource, $tmp, 100) or die('ERROR : jpeg');

            $tmp = '~sdelaher/' . $donnees['photo'];
            $img = deletePattern('1-', $tmp);
            echo '<td style="width:200px; height:100px;"><a href="' . $img . '"><img src="' . $tmp . '"; alt="Cliquer pour agrandir"; style=" display:block; margin:0 auto";/></a></td>';
        } else {
             $tmp = '~sdelaher/' . $donnees['photo'];
             $img = deletePattern('1-', $tmp);
            echo '<td style="width:200px; height:100px;"><a href="' . $img . '"><img src="' . $tmp . '"; alt="Cliquer pour agrandir"; style=" display:block; margin:0 auto";/></a></td>';
        }

        echo '
                    </tr>   
               <tr>
                   <th>Promotion</th>';
                   if($promo)
                       echo '<td>Oui</td>';
                   else echo '<td>Non</td>';

                   echo '
               </tr>
               <tr>
                   <th>Catégorie</th>';
                   $command2=$bdd->prepare('SELECT libelle FROM categorie WHERE num_categorie=:categorie');
                   $command2->bindParam(':categorie', $donnees['num_categorie']);
                   $command2->execute() or die(print_r($command2->errorInfo()));

                   $categorie = $command2->fetch();
                   echo'
                   <td>' . $categorie['libelle'] .'</td>
               </tr>
               <tr>
                    <th>Panier</th>';
                   
                        echo '<td style="width:200px; height:100px;"><a href="pages/panier.php?action=ajout&amp;l=' . $donnees['nom_produit'] . '&amp;p=' . $donnees['prix'] . '" onclick="window.open(this.href, \'\', 
                                \'toolbar=no, location=no, directories=no, status=yes, scrollbars=yes, resizable=yes, copyhistory=no, width=600, height=350\'); return false;">Ajouter au panier</a>
                              </td>';

                    echo '</tr>
            </table></td>';
            
            $cmp++;
            if($cmp == 4)
            {
                echo '</tr>';
                $cmp = 0;
            }
            $i++;
       }
       if ($i == 0) {
        echo '<td>Aucun produit actuellement dans cette catégorie</td></tr>';
    } 
    else {
        echo'</tr>';
    }
}

   
               function deletePattern($pattern, $string)
            {
                $str='';
                
                $verif;
                $verif[0]=false;
                $verif[1]=false;
                
                for($i=0;$i<strlen($string);$i++)
                {

                     if($string[$i]!=$pattern[0] && $string[$i]!=$pattern[1])
                           $str=$str . $string[$i];
                       
                     if($string[$i]==$pattern[0])
                           $verif[0]=true;
                       
                     if($string[$i]==$pattern[1])
                           $verif[1]=true;
                       
                       if($verif[0]==true && $verif[1]==true)
                           break;
                     
                }
                
                for($i++;$i<strlen($string);$i++)
                {
                    $str = $str . $string[$i];
                }
                return $str;
            }
   ?>