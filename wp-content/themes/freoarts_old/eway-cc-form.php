<div>
    <p class="form-row form-row-wide">
        <label for="EWAY_CARDNAME"><?php _e( 'Card Name', 'wc-eway' ); ?><span class="required">*</span></label>
        <input id="EWAY_CARDNAME" class="input-text wc-credit-card-form-card-name" type="text" autocomplete="off" placeholder="" name="EWAY_CARDNAME" />
    </p>
    <p class="form-row form-row-wide">
	    
        <label for="EWAY_TEMPCARDNUMBER"><?php _e( 'Card Number', 'wc-eway' ); ?><span class="required">*</span> <img src="/wp-content/themes/freoarts/img/ui/master-card.svg" alt="Master card" style="float: right; margin: 0 0 8px 5px;"> <img src="/wp-content/themes/freoarts/img/ui/visa.svg" style="float: right; margin: 0 0 8px 0;" alt="Visa">  </label>
        <input id="EWAY_TEMPCARDNUMBER" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="•••• •••• •••• ••••" name="EWAY_TEMPCARDNUMBER" />
    </p>
    <p class="form-row form-row-first">
        <label for="EWAY_EXPIRY"><?php _e( 'Expiry (MM/YY)', 'wc-eway' ); ?><span class="required">*</span> </label>
        <input id="EWAY_EXPIRY" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="MM / YY" name="EWAY_EXPIRY" />
    </p>
    <p class="form-row form-row-last">
        <label for="EWAY_CARDCVN"><?php _e( 'Card CVN Code', 'wc-eway' ); ?><span class="required">*</span></label>
        <input id="EWAY_CARDCVN" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="CVN" name="EWAY_CARDCVN" />
    </p>
</div>

<div class="notification" style="clear: both;padding-top: 10px;">
    <p style="clear: both;
    padding: 20px 30px;
    border: 2px solid #f38231;
    font-size: 14px;
    color: #f38231;
    background: white;
    font-weight: 600;">Upon clicking the "Confirm And Pay" button below, your payment will be processed securely via the eWAY payment gateway. To ensure your order is completed successfully please ensure you do not leave this page until you have been automatically redirected to a receipt page.</p>
</div>

<input type="submit" value="<?php _e('Confirm and Pay', 'wc-eway'); ?>" class="submit buy button">