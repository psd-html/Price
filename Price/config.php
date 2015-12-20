<?php 
if(!defined('PLX_ROOT')) exit;
# Control du token du formulaire
plxToken::validateFormToken($_POST);
# nombre de prices existants
$nbprice = floor(sizeof($plxPlugin->getParams())/2);
if(!empty($_POST)) {

    # configuration de la page 
    if (!empty($_POST['mnuDisplay']) AND !empty($_POST['mnuName']) AND !empty($_POST['mnuPos']) AND !empty($_POST['template']))  {

        $plxPlugin->setParam('mnuInfo', $_POST['mnuInfo'], 'cdata');
        $plxPlugin->setParam('mnuDisplay', $_POST['mnuDisplay'], 'numeric');
        $plxPlugin->setParam('mnuName', $_POST['mnuName'], 'cdata');
        $plxPlugin->setParam('mnuPos', $_POST['mnuPos'], 'numeric');
        $plxPlugin->setParam('template', $_POST['template'], 'string');
        $plxPlugin->saveParams();
    }

	if (!empty($_POST['option-new']) AND !empty($_POST['price-new']))  {
        # création d'un nouveau price
        $newprice = $nbprice + 1;
		$plxPlugin->setParam('option'.$newprice, plxUtils::strCheck($_POST['option-new']), 'cdata');
		$plxPlugin->setParam('price'.$newprice, plxUtils::strCheck($_POST['price-new']), 'cdata');
		$plxPlugin->setParam('active'.$newprice, plxUtils::strCheck($_POST['active-new']), 'cdata');
        $plxPlugin->saveParams();
        
	}else{
        
        # Mise à jour des prices existants
        for($i=1; $i<=$nbprice; $i++) {
            if($_POST['delete'.$i] != "1" AND !empty($_POST['option'.$i]) AND !empty($_POST['price'.$i])){ // si on ne supprime pas et que les prices ne sont pas vide
                
                #mise a jour du option et price
                $plxPlugin->setParam('option'.$i, plxUtils::strCheck($_POST['option'.$i]), 'cdata');
                $plxPlugin->setParam('price'.$i, plxUtils::strCheck($_POST['price'.$i]), 'cdata');
                $plxPlugin->setParam('active'.$i, plxUtils::strCheck($_POST['active'.$i]), 'cdata');
                $plxPlugin->saveParams();
            
            }elseif($_POST['delete'.$i] == "1"){
                $plxPlugin->setParam('option'.$i, '', '');
                $plxPlugin->setParam('price'.$i, '', '');
                $plxPlugin->setParam('active'.$i, '', '');
                $plxPlugin->saveParams();
            }
        }
    }
}
    # mise à jour du nombre de membres existants
    $nbmembres = floor(sizeof($plxPlugin->getParams())/2);
    

    $mnuDisplay =  $plxPlugin->getParam('mnuDisplay')=='' ? 1 : $plxPlugin->getParam('mnuDisplay');
    $mnuName =  $plxPlugin->getParam('mnuName')=='' ? 'Price' : $plxPlugin->getParam('mnuName');
    $mnuPos =  $plxPlugin->getParam('mnuPos')=='' ? 2 : $plxPlugin->getParam('mnuPos');
    $template = $plxPlugin->getParam('template')=='' ? 'static.php' : $plxPlugin->getParam('template');


    # On récupère les templates des pages statiques
    $files = plxGlob::getInstance(PLX_ROOT.'themes/'.$plxAdmin->aConf['style']);
    if ($array = $files->query('/^static(-[a-z0-9-_]+)?.php$/')) {
        foreach($array as $k=>$v)
            $aTemplates[$v] = $v;
    }

    # mise à jour du nombre de prices existants
	$nbprice = floor(sizeof($plxPlugin->getParams())/2);
?>

<style>
    input, textarea {border-radius: 5px}
    input.option{width: 70%}
    input.numeric{width: 100px}
    textarea {min-height: 50px}
    label{font-style: italic}
    td>input{width: 100%}
</style>

<!-- navigation sur la page configuration du plugin -->
<nav id="tabby-1" class="tabby-tabs" data-for="example-tab-content">
	<ul>
		<li><a data-target="tab1" class="active" href="#"><?php $plxPlugin->lang('L_NAV_LIEN1') ?></a></li>
		<li><a data-target="tab2" href="#"><?php $plxPlugin->lang('L_NAV_LIEN2') ?></a></li>
		<li><a data-target="tab3" href="#"><?php $plxPlugin->lang('L_NAV_LIEN3') ?></a></li>
	</ul>
</nav>

<!-- contenu de la page configuration -->
<div class="tabby-content" id="example-tab-content">


<!-- page pour afficher les témoignages -->
<div data-tab="tab1">

    <h2><?php $plxPlugin->lang('L_NAV_LIEN1') ?></h2>

    <div class="formulaire">
        <!-- prices déja créés -->
        <form action="parametres_plugin.php?p=Price" method="post">
            <fieldset>
                <table class="full-width">
                    <thead>
                        <tr>
                            <th class="id"><?php $plxPlugin->lang('L_TAB_1') ?></th>
                            <th><?php $plxPlugin->lang('L_TAB_2') ?></th>
                            <th><?php $plxPlugin->lang('L_TAB_3') ?></th>
                            <th class="checkbox"><?php $plxPlugin->lang('L_TAB_4') ?></th>
                            <th class="checkbox"><?php $plxPlugin->lang('L_TAB_5') ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php for($i=1; $i<=$nbprice; $i++) {?>
                        <?php $option = $plxPlugin->getParam(option.$i);
                        if(!empty($option)) { ?>
                        
                        <tr class="line-<?php echo $i%2 ?>">
                            <td>
                                <?php echo $i; ?>
                            </td>
                            
                            <td class="option">
                            	<input id="option" name="option<?= $i; ?>"  maxlength="255" value="<?= $plxPlugin->getParam(option.$i); ?>">
                            </td>
                            
                            <td class="price">
                            	<input id="option" name="price<?= $i; ?>"  maxlength="255" value="<?= $plxPlugin->getParam(price.$i); ?>">
                            </td>

                            <td class="price">
                                <select name="active<?php echo $i; ?>">
                                  <option value="true" <?php if ($plxPlugin->getParam(active.$i) == "true"){ echo'selected';}?>>Oui</option> 
                                  <option value="false" <?php if ($plxPlugin->getParam(active.$i) == "false"){ echo'selected';}?>>Non</option>
                                </select>
                            </td>
                            
                            <td class="checkbox">
                                <input type="checkbox" name="delete<?= $i; ?>" value="1">
                            </td>
                        </tr>
                            <?php }; ?>
                                <?php }; ?>
                    </tbody>

                </table>
            </fieldset>

                    <p class="in-action-bar">
                        <?php echo plxToken::getTokenPostMethod() ?>
                        <input class="bt" type="submit" name="submit" value="<?php $plxPlugin->lang('L_FORM_BT1') ?>" />
                    </p>
        </form>
    </div>

</div><!-- de la page 1 -->

<!-- page pour créer un témoignage -->
<div data-tab="tab2">

<h2><?php $plxPlugin->lang('L_NAV_LIEN2') ?></h2>

<div class="new">

    <form action="parametres_plugin.php?p=Price" method="post">
        <p>
            <label for="option"><?php $plxPlugin->lang('L_FORM_1') ?></label>
             <input class="option" type="text" name="option-new" value="" />
        </p>

        <p>
            <label for="price"><?php $plxPlugin->lang('L_FORM_2') ?></label>
             <input class="numeric" type="text" name="price-new" value="" />
        </p>

           
        <p class="in-action-bar">
            <?php echo plxToken::getTokenPostMethod() ?>
            <input class="bt" type="submit" name="submit" value="<?php $plxPlugin->lang('L_FORM_BT2') ?>" />
        </p>

    </form>
</div>

</div><!-- fin de la page 2 -->

<!-- page de configuration -->
<div data-tab="tab3">
    <h2><?php $plxPlugin->lang('L_NAV_LIEN3') ?></h2>

        <form action="parametres_plugin.php?p=Price" method="post">

            <p>
                <label for="id_content">Texte en haut de page</label>
                <textarea id="id_content" rows="5"  name="mnuInfo"><? echo $plxPlugin->getParam('mnuInfo'); ?></textarea>
            </p>

            <p>
                <label for="mnuDisplay">Afficher la page dans la navigation</label>
                <select name="mnuDisplay" id="mnuDisplay">
                    <option value="1"  <?php if ($mnuDisplay == '1') { echo'selected';}?> >Oui</option>
                    <option value="0" <?php if ($mnuDisplay == '0') { echo'selected';}?> >Non</option>
                </select>

            <p>
                <label for="mnuName">Titre de la page</label>
                <input id="mnuName" name="mnuName"  maxlength="255" value="<?php echo $plxPlugin->getParam("mnuName"); ?>">
            </p>
            <p>
                <label for="mnuPos">Position de la page</label>
                <input id="mnuPos" name="mnuPos"  maxlength="255" value="<?php echo $plxPlugin->getParam("mnuPos"); ?>">
            </p>

            <p>
                <label for="template">Template de votre page</label>
                <?php plxUtils::printSelect('template', $aTemplates, $template) ?>
            </p>


            <p class="in-action-bar">
                <?php echo plxToken::getTokenPostMethod() ?>
                <input type="submit" name="submit" value="<?php $plxPlugin->lang('L_FORM_BT2') ?>" />
            </p>

        </form>    


    </div><!-- fin de la page 3 -->


</div>




<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="<?php echo PLX_PLUGINS ?>Price/app/jquery.tabby.js"></script>

<script>
    $(document).ready(function(){
        $('#tabby-1').tabby();
    });
</script>

