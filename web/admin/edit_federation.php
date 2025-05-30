<?php
/*
 * *****************************************************************************
 * Contributions to this work were made on behalf of the GÉANT project, a 
 * project that has received funding from the European Union’s Framework 
 * Programme 7 under Grant Agreements No. 238875 (GN3) and No. 605243 (GN3plus),
 * Horizon 2020 research and innovation programme under Grant Agreements No. 
 * 691567 (GN4-1) and No. 731122 (GN4-2).
 * On behalf of the aforementioned projects, GEANT Association is the sole owner
 * of the copyright in all material which was developed by a member of the GÉANT
 * project. GÉANT Vereniging (Association) is registered with the Chamber of 
 * Commerce in Amsterdam with registration number 40535155 and operates in the 
 * UK as a branch of GÉANT Vereniging.
 * 
 * Registered office: Hoekenrode 3, 1102BR Amsterdam, The Netherlands. 
 * UK branch address: City House, 126-130 Hills Road, Cambridge CB2 1PQ, UK
 *
 * License: see the web/copyright.inc.php file in the file structure or
 *          <base_url>/copyright.php after deploying the software
 */

/**
 * This page edits a federation.
 * 
 * @author Stefan Winter <stefan.winter@restena.lu>
 */
?>
<?php
require_once dirname(dirname(dirname(__FILE__))) . "/config/_config.php";

$auth = new \web\lib\admin\Authentication();
$deco = new \web\lib\admin\PageDecoration();
$validator = new \web\lib\common\InputValidation();
$uiElements = new web\lib\admin\UIElements();

$auth->authenticate();

$fedPost = $_POST['fed_id'];

$wizard = new \web\lib\admin\Wizard(false);
$wizard->setMessages();

[$my_fed, $editMode] = $validator->existingFederationInt($fedPost, $_SESSION['user']);
$fed_options = $my_fed->getAttributes();
$availableFedOptions = \web\lib\admin\OptionDisplay::enumerateOptionsToDisplay("fed", $my_fed->tld);
$wizard->setOptionsHelp($availableFedOptions);
$wizard->setMessages();



/// product name (eduroam CAT), then term used for "federation", then actual name of federation.
echo $deco->defaultPagePrelude(sprintf(_("%s: Editing %s '%s'"), \config\Master::APPEARANCE['productname'], $uiElements->nomenclatureFed, $my_fed->name));
$langObject = new \core\common\Language();
?>
<script src="js/XHR.js" type="text/javascript"></script>
<script src="js/option_expand.js" type="text/javascript"></script>
<script type="text/javascript" src="../external/jquery/jquery-ui.js"></script> 
<link rel="stylesheet" type="text/css" href="../external/jquery/jquery-ui.css" />
<script type="text/javascript" src="js/wizard.js"></script> 
<link rel='stylesheet' type='text/css' href='css/wizard.css.php' />
<?php
if ($editMode == 'readonly') {
    print('<style>'
            . 'button.newoption {visibility: hidden}'
            . 'input {pointer-events: none} '
            . '.ui-sortable-handle {pointer-events: none}'
            . '</style>');
}
?>
</head>
<body>

    <?php echo $deco->productheader("FEDERATION"); ?>
    <div id="wizard_help_window"><img id="wizard_menu_close" src="../resources/images/icons/button_cancel.png" ALT="Close"/><div></div></div>
    <h1>
        <?php
        /// nomenclature for federation, then actual federation name
        printf(_("Editing %s information for '%s'"), $uiElements->nomenclatureFed, $my_fed->name);
        ?>
    </h1>
    <div class='infobox'>
        <h2><?php $tablecaption = sprintf(_("%s Properties"),$uiElements->nomenclatureFed); echo $tablecaption?></h2>
        <table>
            <caption><?php echo $tablecaption;?></caption>
            <tr>
                <th class="wai-invisible" scope="col"><?php echo _("Property Type");?></th>
                <th class="wai-invisible" scope="col"><?php echo _("Language if applicable");?></th>
                <th class="wai-invisible" scope="col"><?php echo _("Property Value");?></th>
            </tr>
            <tr>
                <td><?php echo _("Country:"); ?></td>
                <td></td>
                <td><strong><?php echo $my_fed->name; ?></strong></td>
            </tr>
            <?php echo $uiElements->infoblock($fed_options, "fed", "FED"); ?>
        </table>
    </div>
    <?php
    echo "<form enctype='multipart/form-data' action='edit_federation_result.php?fed_id=$my_fed->tld" . "' method='post' accept-charset='UTF-8'>
              <input type='hidden' name='MAX_FILE_SIZE' value='" . \config\Master::MAX_UPLOAD_SIZE . "'>";
    ?>
    <fieldset class="option_container">
        <legend><strong><?php echo sprintf(_("%s Properties"),$uiElements->nomenclatureFed); ?></strong></legend>
        <?php
        $options = [
            'level' => 'fed',
            'fed_id'=> $my_fed->tld
        ];
        echo $wizard->displayHelp("fed_general", $options);
        $optionDisplay = new \web\lib\admin\OptionDisplay($fed_options, \core\Options::LEVEL_FED);
        echo $optionDisplay->prefilledOptionTable("fed", $my_fed->tld);
        ?>
        <button type='button' class='newoption' onclick='getXML("fed", "<?php echo $my_fed->tld ?>")'><?php echo _("Add new option"); ?></button>
    </fieldset>
    <?php
    echo "<div>";
    if ($editMode === 'fullaccess') {
        echo "<button type='submit' name='submitbutton' value='" . web\lib\common\FormElements::BUTTON_SAVE . "'>" . _("Save data") . "</button>";
        $discardLabel = _("Discard changes");
    } else {
        $discardLabel = _("Return");
    }
    echo "<button type='button' class='delete' name='abortbutton' value='abort' onclick='javascript:window.location = \"overview_federation.php?fed_id=$fedPost\"'>" . $discardLabel . "</button></div></form>";
    echo $deco->footer();
