<?php

$requete = $this->pdo->prepare("SELECT * FROM salarie
                                    where id = ?");

    $requete->execute([$args['idsalarie']]);
    $lignes = $requete->fetchAll();
    //dump($lignes);
    //Récupéation des valeurs pour affichage dans fiche du salariés
        //table salarie
    $idsalarie            = $args['idsalarie'];
    foreach( $lignes as $ligne){
      $nom                  = $ligne[nom];
      $prenom               = $ligne[prenom];
      $idpole               = $ligne[idpole];
      $matricule            = $ligne[matricule];
      $datenaissance        = $ligne[datenaissance];
      $lieunaissance        = $ligne[lieunaissance];
      $dptnaissance         = $ligne[dptnaissance];
      $genre                = $ligne[genre];
      $adresse1             = $ligne[adresse1];
      $adresse2             = $ligne[adresse2];
      $villeadresse         = $ligne[villeadresse];
      $codepostaladresse    = $ligne[codepostaladresse];
      $datedebcontrat       = $ligne[datedebcontrat];
      $datefininitial       = $ligne[datefininitial];
      $ress                 = $ligne[ressources];
      $id_lieu_travail      = $ligne[id_lieu_travail];
      $motifsortie          = $ligne[motifsortie];
      $datesortie           = $ligne[datesortie];
      $detailsortie         = $ligne[detailsortie];
      $typesortie           = $ligne[typesortie];
      $detailsortie         = $ligne[detailsortie];
      $id_user              = $ligne[id_user];
      $dateametra           = $ligne[dateametra];
      $telephone            = $ligne[telephone];
      $paysnaissance        = $ligne[paysnaissance];
      $nationalite          = $ligne[nationalite];
      $datesejour           = $ligne[datesejour];
      $contact              = $ligne[contact];
      $poleemploi           = $ligne[poleemploi];   
      $datepoleemploi       = $ligne[datepoleemploi];
      $securitesociale      = $ligne[securitesociale];
      $niveauetude          = $ligne[niveauetude];
      $qpv                  = $ligne[qpv];
      $prescripteur         = $ligne[prescripteur];
      $referent             = $ligne[referent];
      $telreferent          = $ligne[telreferent];
      $mailreferent         = $ligne[mailreferent];
      $adressereferent      = $ligne[adressereferent];
      $rqth                 = $ligne[rqth];
      $numcaf               = $ligne[numcaf];
      $dureechomage         = $ligne[dureechomage];
      $situfa               = $ligne[situfamiliale];
      $enfants              = $ligne[enfants];
      $locomotion           = $ligne[locomotion];
      $contratpendantperiode= $ligne[contratpendantperiode];
      $droitdif             = $ligne[droitdif];
      $nbreheuredif         = $ligne[nbreheuredif];
      $sommedif             = $ligne[sommedif];
      $attestationcert      = $ligne[attestationcert];
      $poleemploi           = $ligne[poleemploi];
      $permis               = $ligne[permis];
      $numagrementpole      = $ligne[numagrementpole];
      $btp                  = $ligne[cartebtp];
      $dpae                 = $ligne[dpae];
      $numurssaf            = $ligne[numurssaf];
      $datefse              = $ligne[datefse];
      $idfse                = $ligne[idfse];
      $datesortiefse        = $ligne[datesortiefse];
      $mailsalarie          = $ligne[mailsalarie];
      $trancheage           = $ligne[trancheage];
      $obspermis            = $ligne[obspermis];
      $idextranet           = $ligne[idextranet];
      $datemutuelle         = $ligne[datemutuelle];
      $mutuelle             = $ligne[mutuelleforce];
      $portabilite          = $ligne[portabilite];
      $droitimage           = $ligne[droitimage];
     
      
        
      $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
      $requete->execute([$id_lieu_travail]);
      $lieu = $requete->fetchAll();
      $lieutravail = $lieu[0][0];
      // dump($lieu);
            //table locomotion
      $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
      $requete->execute([$locomotion]);
      $loco = $requete->fetchAll();
      $locomotion = $loco[0][0];
      //table situfamiliale => situation familiale
      $requete = $this->pdo->prepare(" SELECT situation FROM situfamiliale WHERE idsitu = ?");
      $requete->execute([$situfa]);
      $situ = $requete->fetchAll();
      $situfamiliale = $situ[0][0];
      //table niveauetude => niveau d'études
      $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE idetude = ?");
      $requete->execute([$niveauetude]);
      $niveau = $requete->fetchAll();
      $niveauetude = $niveau[0][0];
      //table ressources
      $requete = $this->pdo->prepare(" SELECT ressources FROM ressources WHERE idressources = ?");
      $requete->execute([$ressources]);
      $ressource = $requete->fetchAll();
      $ressources = $ressource[0][0];
      //table renouvellement=>affichage renouvellement date fin contrat
      $requete = $this->pdo->prepare("SELECT * FROM renouvellement WHERE idsalarie = ? ORDER BY nouvelle_date ASC");
      $requete->execute([$idsalarie]);
      $renew = $requete->fetchAll();


      //table documents => liste des documents à fournir par le salarié
       $requete = $this->pdo->prepare("SELECT * FROM documents
                                     ");
       $requete->execute();
       $docs = $requete->fetchAll();
       foreach ($docs as $doc) {
         $requete = $this->pdo->prepare("UPDATE  documents
                                        SET coche = null
                                      ");
         $requete->execute();
       }
       //table docsafournirsalarie => liste des documents déjà fournis par le salarié
       $requete = $this->pdo->prepare("SELECT * FROM docsafournirsalarie
                                       WHERE idsalarie=?
                                     ");
       $requete->execute([$idsalarie]);
       $salers = $requete->fetchAll();
       foreach ($salers as $saler){


         $requete = $this->pdo->prepare("UPDATE  documents
                                        SET coche = $idsalarie
                                        WHERE documents.iddoc = $saler[iddocuments]
                                      ");
         $requete->execute();
       
       }


       ////table datechantier contenant les dates de formation par chantier
        $requete = $this->pdo->prepare("SELECT * FROM datechantier as datchant
                                        LEFT JOIN lieutravail on idlieut = lieutravail.idlieu
                                        LEFT JOIN formation on idformation = idform
                                        WHERE idlieu = ?
                                       ");
        $requete->execute([$id_lieu_travail]);
        $datechantier = $requete->fetchAll();


      ////table cqp contenant liste formation effectuée par salariés
       $requete = $this->pdo->prepare("SELECT * FROM cqp
                                       LEFT JOIN datechantier  on iddate = iddatechant
                                       LEFT JOIN formation on idformation = idform
                                       LEFT JOIN lieutravail on idlieu = datechantier.idlieut
                                       WHERE idsalarie = ?
                                      ");
       $requete->execute([$idsalarie]);
       $dossier = $requete->fetchAll();


       ////table pmsmp contenant pmsmp effectuée par salarié
        $requete = $this->pdo->prepare("SELECT * FROM pmsmp
                                        WHERE idsalarie = ?
                                       ");
        $requete->execute([$idsalarie]);
        $pmsmps = $requete->fetchAll();

////table pmsmp contenant pmsmpprol effectuée par salarié
        $requete = $this->pdo->prepare("SELECT * FROM pmsmpprol
                                        WHERE idsalarie = ?
                                        ORDER BY idpmsmp ASC
                                       ");
        $requete->execute([$idsalarie]);
        $pmsmpprols = $requete->fetchAll();
         
        ////table zone
         $requete = $this->pdo->prepare("SELECT * FROM zone
                                        ");
        $requete->execute();
        $zones = $requete->fetchAll();

////table ressource
        $requete = $this->pdo->prepare("SELECT * FROM ressources
               ");
         $requete->execute();
        $ressources = $requete->fetchAll();

      $requete = $this->pdo->prepare("SELECT * FROM formation

                                      ");
      $requete->execute();
      $formations = $requete->fetchAll();
      foreach( $formations as $formation){
        $idform=$formation[idformation];
      }
      $requete = $this->pdo->prepare("SELECT * FROM documents
                                      ORDER by documents ASC  
                                   ");
      $requete->execute();
      $docs = $requete->fetchAll();

      // dump($idform);
      $idform=$idform+1;
      // dump($idform);

      $requete = $this->pdo->prepare("SELECT * FROM locomotionsalarie
                                      LEFT JOIN locomotion on idlocomotion = locomotion.id

                                      WHERE idsal = ?
                                   ");
      $requete->execute([$idsalarie]);
      $locosalarie = $requete->fetchAll();
      $requete = $this->pdo->prepare("SELECT * FROM permissalarie
                                      LEFT JOIN permis on idperm = permis.idpermis

                                      WHERE idsal = ?
                                   ");
      $requete->execute([$idsalarie]);
      $permsalarie = $requete->fetchAll();

      //table sanctionsalarie
      $requete = $this->pdo->prepare("SELECT * FROM sanctionsalarie
      LEFT JOIN sanction on sanctionsalarie.sanction = sanction.idsanction
      LEFT JOIN motifsanction on sanctionsalarie.motif = motifsanction.idmotifsanction
      WHERE idsalarie = ?
      ");
      $requete->execute([$args['idsalarie']]);
      $sanctionsals = $requete->fetchAll();

      $requete = $this->pdo->prepare(" SELECT lieu FROM lieutravail WHERE idlieu = ?");
      $requete->execute([$id_lieu_travail]);
      $lieu = $requete->fetchAll();
      $lieutravail = $lieu[0][0];
      // dump($lieu);
      // dump(  $lieutravail );
      //table locomotion
      $requete = $this->pdo->prepare(" SELECT locomotion FROM locomotion WHERE id = ?");
      $requete->execute([$locomotion]);
      $loco = $requete->fetchAll();
      $locomotion = $loco[0][0];
     
      //table niveauetude => niveau d'études
      $requete = $this->pdo->prepare(" SELECT niveau FROM niveauetude WHERE idetude = ?");
      $requete->execute([$niveauetude]);
      $niveau = $requete->fetchAll();
      $niveauetude = $niveau[0][0];
      //table ressources
      $requete = $this->pdo->prepare(" SELECT ressources FROM ressources WHERE idressources = ?");
      $requete->execute([$ress]);
      $ressource = $requete->fetchAll();
      $ressources = $ressource[0][0];

      if ($genre!="Mr"){
        $civilite="Madame";
      }else{
        $civilite="Monsieur";
      }
      if ($rqth == 0){
        $rqth ="NON";
      }else{
        $rqth ="OUI";
      }

      if ($btp == 1){
        $btp="OUI";
      }else{
        $btp="NON";
      }
      if ($droitimage == 1){
        $droitimage="OUI";
      }else{
        $droitimage="NON";
      }
      


    }
?>