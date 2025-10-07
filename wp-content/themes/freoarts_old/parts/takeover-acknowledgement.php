<?php

$title = ifne($GLOBALS['theme_options'], 'acknowledgement_title') ?
    $GLOBALS['theme_options']['acknowledgement_title'] :
    'Fremantle Arts Centre is situated at Walyalup on Whadjuk Nyoongar Boodjar. ';
$text = ifne($GLOBALS['theme_options'], 'acknowledgement_text') ?
    $GLOBALS['theme_options']['acknowledgement_text'] :
    'We acknowledge the Whadjuk people as the traditional owners and custodians of these lands and waterways and extend our respect to their Elders, past and present.';
$text2 = ifne($GLOBALS['theme_options'], 'acknowledgement_text2') ?
    $GLOBALS['theme_options']['acknowledgement_text2'] :
    'We offer our heartfelt gratitude to the Whadjuk community and to all Aboriginal and Torres Strait Islander people who continue to care for Country and share their knowledge – this generosity and wisdom helps us to understand and navigate Country safely and respectfully.';

?>

<?php if( ifne($GLOBALS['theme_options'], 'show_acknowledgement') == true ) { ?>

    <div class="u-vis-hide">
        <a id="takeover-modal-trigger" href="#takeover-modal">
            <span class="u-vis-hide">Open the Acknowledgement of Country modal</span>
        </a>
        <div id="takeover-modal">
            <div class="container container--gutters">
                <div class="takeover-modaal__content">
                    <h1 class="title title--h1"><?= $title ?></h1>
                    <div class="columns">
                        <strong><?= $text ?></strong>
                        <?= $text2 ?>
                    </div>
                        
                    <button type="button" class="takeover-modaal__dismiss active btn-white">
                        <span class="text">Continue</span>
                        <span class="icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php } ?>