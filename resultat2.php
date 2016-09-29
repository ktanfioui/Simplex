<html>
<head>
	<title>Simplexe en PHP </title>
	<link rel="icon" type="image/png" href="calcul.png" />
	<meta charset="utf-8">
	<link rel="stylesheet" href="style.css"/>

</head>


<?php //fontions
ini_set("display_errors",0);
error_reporting(0);
include ("fonctions.php");
?>

<body>
<h1><center> Simplexe en PHP </center></h1>
<br><br><br>

<?php

session_start();
$n=$_SESSION['vari'];
$m=$_SESSION['cont'];



//creation du tableau de simplexe et le remplissage
//case [0][0]
$tab[0][0]="tableau";

//case Cout
$tab[$m+1][0]="Coût";

//remplire la premiere ligne du tableau par les X1 X2....
for($i=1;$i<=$n;$i++)
{
	$tab[0][$i]="X".$i;
}


//inserer la matrice A dans le tab
$p=0;
for($i=1;$i<=$m;$i++)
{
	for($j=1;$j<=$n;$j++)
	{
		$tab[$i][$j]=$_POST['A'][$p];
		$p++;
	}
}


//un compteur sur (inf)
$compt1=0;
//un compteur sur (sup)
$compt2=0;
//un compteur sur (egal)
$compt3=0;

for($i=1;$i<=$m;$i++)
{
	if($_POST['borne'][$i-1]=="inf")
	{
		$compt1++;
	}
	if($_POST['borne'][$i-1]=="sup")
	  {$compt2++;} 
	  	
	if($_POST['borne'][$i-1]=="egal")
	  {$compt3++;}
}

/*test sur compt
echo "les inf".$compt1;
echo "les sup".$compt2;
echo "les egal".$compt3;
*/

//premiere ligne vec les e et les a
//ajout des var d'écart et var artificielle
$i=1;
for($j=1;$j<=$m;$j++)
{
	if($_POST['borne'][$j-1]=="sup")
	{
		$tab[0][$i+$n]="e".$j;
		$i=$i+1;
		$tab[0][$i+$n]="a".$j;
		//$k=$k+1;
	}
	elseif($_POST['borne'][$j-1]=="inf")
	{
		$tab[0][$i+$n]="e".$j;
		
		
	}
	else//($_POST['borne'][$i-1]=="egal")
	{
		$tab[0][$i+$n]="a".$j;
		
	}
	$i=$i+1;
}



//ajouter B dans la premiere ligne
$tab[0][$n+1+$compt1+2*$compt2+$compt3]="B";


for($i=1;$i<=$m;$i++)//i pour les lignes
{
	if($_POST['borne'][$i-1]=="inf")
	{
       $tab[$i][0]="e".$i;//premier colonne vec les e en cas d'inf
       for($k=$n+1;$k<=$n+1+$compt1+2*$compt2+$compt3;$k++)//k pour les colonnes
       {
       	if($tab[$i][0]==$tab[0][$k])
       	{
       		$tab[$i][$k]=1;
       	}
       	else
       	{
       		$tab[$i][$k]=0;
       	}
       }
	}
	elseif($_POST['borne'][$i-1]=="egal")
	{
       $tab[$i][0]="a".$i;
       for($k=$n+1;$k<=$n+1+$compt1+2*$compt2+$compt3;$k++)
       {
       	if($tab[$i][0]==$tab[0][$k])
       	{
       		$tab[$i][$k]=1;
       	}
        else
       	{
       	    $tab[$i][$k]=0;
       	}
       }
	}
	else
	{
		$tab[$i][0]="a".$i;
		for($k=$n+1;$k<=$n+1+$compt1+2*$compt2+$compt3;$k++)
       {
       	if($tab[$i][0]==$tab[0][$k])
       	{
       		$tab[$i][$k]=1;
			$tab[$i][$k-1]=-1;
       	}
        else
       	{
       	    $tab[$i][$k]=0;
       	}
       }
		
	}
}


//remplir le tableau avec la matrice b[]
$k=0;
for($i=1;$i<=$m;$i++)
{
	$tab[$i][$n+$compt1+2*$compt2+$compt3+1]=$_POST['b'][$k];
	$k++;
}


//remplir la ligne des couts
//cas du simplexe 
if($compt1==$m)
{
   $tab[$m+1][0]="Coût";
    $j=0;
   for($i=1;$i<=$n;$i++)
   {
	$tab[$m+1][$i]=-($_POST['cout'][$j]);
	$j++;
   }

    for($i=1;$i<=$compt1+2*$compt2+$compt3;$i++)
   {
	$tab[$m+1][$i+$n]=0;
   }	
}
else//cas de 2 phases
{
	echo "<center>Probleme de Simplexe de 2 Phase</center> ";
	$tab[$m+1][0]="Coût";
   
     for($i=1;$i<=$n+$compt1+2*$compt2+$compt3;$i++)
	 {
		 if($tab[0][$i][0]=='a')
		 {
			 $tab[$m+1][$i]=-1;
		 }
		 else
		 {
			  $tab[$m+1][$i]=0;
		 }
	 }
	
	
}


//print_r($tab);


//affichage du tableau

affichage($tab,$m,$n+$compt1+2*$compt2+$compt3);
echo "<br/><br/>";

//calcul de Z finale
function Z($tab,$n,$m,$nbr)
{
 $v=resultatX($tab,$n,$m,$nbr);
 $Z=0;
 echo "La Solution est : ";
for($i=1;$i<=$n;$i++)
{
	$Z=$Z+$v[$i]*($_POST['cout'][$i-1]);
	echo "        X".$i."=".$v[$i];
	
}
echo "<br/>";

echo "Le bénéfice est : Z = ".$Z;
	
}

function verifierRatio($tab,$n,$m,$nbr)
{
	$ratio=ratio($tab,$n,$m,$nbr);
$p=0;
for($i=1;$i<=$m;$i++)
{
	//$p=0;
	if($ratio[$i]<=-2)
	{
		$p++;
	}
}
return $p;
}	

/////verifier si c un prob de simplexe ou 2 phases
if($compt1==$m)
{

////////////////////////////////boucle simplexe////////////////////////////
$p=verifierRatio($tab,$n,$m,$n+$compt1+2*$compt2+$compt3);
if($p==$m)
{
	echo '<h3><p style="color: red;">Problème non borné</p></h3>';
}
else
{
	do {
	affichagePivot($tab,$n,$m,$n+$compt1+2*$compt2+$compt3);
	$tab2=echelonner($tab,$n,$m,$n+$compt1+2*$compt2+$compt3);
	affichage($tab2,$m,$n+$compt1+2*$compt2+$compt3);
	$l=verifier($tab2,$n,$m,$n+$compt1+2*$compt2+$compt3);
	$tab=$tab2;
    $g=verifierRatio($tab,$n,$m,$n+$compt1+2*$compt2+$compt3);
	Z($tab,$n,$m,$n+$compt1+2*$compt2+$compt3);
    if($g==$m)
	{
		echo "<br/>";
		echo '<h3><p style="color: red;">Problème non borné</p></h3>';
		break;
	}		
}while($l==0);

//Z($tab,$n,$m,$n+$compt1+2*$compt2+$compt3);
}
}

else
{////////PHASE1
	echo "<br/><br/>";
	$tabb=echCout($tab,$n,$m,$n+$compt1+2*$compt2+$compt3);
	//affichage($tabb,$m,$n+$compt1+2*$compt2+$compt3);
	$p1=verifierRatio($tabb,$n,$m,$n+$compt1+2*$compt2+$compt3);
	if($p1==$m)
	{
		echo '<h3><p style="color: red;">Problème non borné</p></h3>';
	}
	else
	{
		do {
	affichagePivot($tabb,$n,$m,$n+$compt1+2*$compt2+$compt3);
	$taba=echelonner($tabb,$n,$m,$n+$compt1+2*$compt2+$compt3);
	//affichage($taba,$m,$n+$compt1+2*$compt2+$compt3);
	$l=verifier($taba,$n,$m,$n+$compt1+2*$compt2+$compt3);
	$tabb=$taba;
    $Za=Za($tabb,$n,$m,$compt2+$compt3,$n+$compt1+2*$compt2+$compt3);
    echo " Za = ".$Za;
    $g1=verifierRatio($tabb,$n,$m,$n+$compt1+2*$compt2+$compt3);
	echo $g1;
     if($g1==$m)
	    {
		    echo "<br/>";
		    //echo '<h3><p style="color: red;">Problème non borné</p></h3>';
		    break;
	    }		
}while($l==0);
	}
	

//Phase 2
if($Za==0)
{
	echo "<br/><br/>";
	echo "<center> DEBUT DE LA PHASE 2 </center> ";

for($k=0;$k<=$n+$compt1+2*$compt2+$compt3;$k++)
{
	if($tabb[0][$k][0]=='a')
	{
		for($i=0;$i<=$m+2;$i++)
		{
			for($j=$k;$j<=$n+$compt1+2*$compt2+$compt3;$j++)
			{
				$tabb[$i][$j]=$tabb[$i][$j+1];
			}
			array_pop($tabb[$i]);
		}
	}
}

echo "<br>";
	//print_r($tabb);
	//ligne du cout du tableau initial de phase2
	$newcout[0]="Coût";
	for($i=1;$i<=$n;$i++)
	{
		$newcout[$i]=-($_POST['cout'][$i-1]);
	}
	for($i=$n+1;$i<=$n+$compt1+$compt2;$i++)
	{
		$newcout[$i]=0;
	}
	///////affiche du tableau initiale de phase deux avec la nouvelle ligne du cout
	$array=$tabb;
	for($j=0;$j<=$n+$compt1+$compt2;$j++)
	{
		$array[$m+1][$j]=$newcout[$j];
		
	}
	affichage($array,$m,$n+$compt1+$compt2);
/////////////Echelonnage de la ligne cout du tableau initiale de la phase 2*$compt2
 $array1=echNewCout($newcout,$array,$n,$m,$compt1+$compt2);
 echo '</br>';
 echo "Nouveau tableau apres modification du cout car ce n'etait pas un vrai tab de simplexe";
  echo '</br>';
  affichage($array1,$m,$n+$compt1+$compt2);
  $p2=verifierRatio($array1,$n,$m,$n+$compt1+$compt2);
  if($p2==$m)
  {
	echo '<h3><p style="color: red;">Problème non borné</p></h3>'; 
  }
  else
  {
	  $f=verifier($array1,$n,$m,$n+$compt1+$compt2);
  if($f==1)
  {
	Z($array1,$n,$m,$n+$compt1+$compt2);  
  }
  else
  {
		//simplexe again
	do {
	affichagePivot($array1,$n,$m,$n+$compt1+$compt2);
	$taba=echelonner($array1,$n,$m,$n+$compt1+$compt2);
	//affichage($taba,$m,$n+$compt1+$compt2);
	$l=verifier($taba,$n,$m,$n+$compt1+$compt2);
	$array1=$taba;	
	Z($array1,$n,$m,$n+$compt1+$compt2);
	$g2=verifierRatio($array1,$n,$m,$n+$compt1+$compt2);
     if($g2==$m)
	    {
		    echo "<br/>";
		    echo '<h3><p style="color: red;">Problème non borné</p></h3>';
		    break;
	    }
   }while($l==0);    
  }
  }
   
}
else
{    
    echo "<br/><br/>";
	echo '<h3><p style="color: red;">Problème non borné</p></h3>';
}
	
}



 //echCout($tab,$n,$m,$n+$compt1+2*$compt2+$compt3);
 //resultatA($tabb,$n,$m,$compt2+$compt3,$n+$compt1+2*$compt2+$compt3);

?>


</body>
</html>