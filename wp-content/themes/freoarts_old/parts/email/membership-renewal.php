<?php
$template_directory_url = get_bloginfo('template_directory');
$site_url = get_bloginfo('url');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>

  <title>Fremantle Arts email Template</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="robots" content="noindex, nofollow">
  <!--[if !mso]><!-->
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!--<![endif]-->

  <style type="text/css">
    .ReadMsgBody {
      width: 100%;
      background-color: #193040;
    }

    .ExternalClass {
      width: 100%;
      background-color: #193040;
    }

    body {
      width: 100%;
      background-color: #f6f6f6;
      margin: 0;
      padding: 0;
      -webkit-font-smoothing: antialiased;
      font-family: Arial, Times, serif
    }

    table {
      border-collapse: collapse !important;
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    @-ms-viewport {
      width: device-width;
    }

    @media only screen and (max-width: 600px) {
      .centerClass {
        margin: 0 auto !important;
      }

      .imgClass {
        width: 100% !important;
        height: auto;
        display: block;
      }

      .wrapper {
        width: 320px;
        padding: 0 !important;
      }

      .header {
        width: 320px;
        padding: 0 !important;
      }

      .container {
        width: 300px;
        padding: 0 !important;
        display: block;
      }

      .mobile {
        width: 300px;
        display: block;
        padding: 0 !important;
        text-align: center !important;
      }

      .mobile-left {
        width: 290px;
        display: block;
        padding: 5px !important;
        text-align: left !important;
      }

      .mobile50 {
        width: 300px;
        padding: 10px 0 !important;
        text-align: center;
        display: block;
      }

      *[class="mobileOff"] {
        width: 0px !important;
        display: none !important;
      }

      *[class*="mobileOn"] {
        display: block !important;
        max-height: none !important;
      }
    }
  </style>

</head>

<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0"
  style="background-color:#f1f1f1; font-family:Arial,serif; margin:0; padding:0; min-width: 100%; -webkit-text-size-adjust:none; -ms-text-size-adjust:none;">

  <!-- Start Background -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f1f1f1">
    <tr>
      <td height="25" style="font-size:25px; line-height:25px;"> </td><!-- Spacer -->
    </tr>
    <tr>
      <td width="100%" valign="top" align="center">

        <!-- Header -->
        <table width="600" height="" cellpadding="0" cellspacing="0" border="0" class="wrapper"
          style="background: #be203a">
          <tr>
            <td height="25" style="font-size:25px; line-height:25px;"> </td><!-- Spacer -->
          </tr>
          <tr>
            <td align="center">
              <!-- Start Container -->
              <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                <tr>
                  <td class="mobile" style="line-height:20px;">
                    <table width="100" cellpadding="0" cellspacing="0" align="center" border="0" class="centerClass">
                      <tr>
                        <td width="200" height="auto" align="center" valign="middle">
                          <a href="https://www.fac.org.au/" target="_blank" style="text-decoration: none; color: #ffffff; display: block; outline: none; border: 0;">
                            <img src="https://wfac.org.au/wp-content/uploads/2025/08/wfac-logo-white.png"
                              alt="FAC Logo"
                              width="200"
                              height="auto"
                              border="0"
                              style="display: block; max-width: 100%; height: auto;">
                          </a>
                        </td>
                      </tr>
                      <tr>
                        <td height="25" style="font-size:25px; line-height:25px;"> </td><!-- Spacer -->
                      </tr>
                    </table>
                  </td>
                </tr>

              </table>
              <!-- End Container -->
            </td>
          </tr>
        </table>
        <table width="600" height="" cellpadding="0" cellspacing="0" border="0" class="wrapper"
          style="background: #ffffff;">
          <tr>
            <td align="center">
              <!-- Start Container -->
              <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                <tr>
                  <td height="30" style="font-size:30px; line-height:30px;"> </td><!-- Spacer -->
                </tr>
                <tr>
                  <td align="center" class="mobile"
                    style="font-family: roboto, helvetica, arial, sans-serif; font-size:20px; line-height:26px; color: #2e2f2f;">
                    Membership Renewal
                  </td>
                </tr>
                <tr>
                  <td height="10" style="font-size:10px; line-height:10px;"> </td><!-- Spacer -->
                </tr>
                <tr>
                  <td align="center" class="mobile"
                    style="font-family:roboto, helvetica, arial, sans-serif; font-size:14px; line-height:20px; color: #2e2f2f;">
                    Your Fremantle Arts Centre membership is due for renewal on <?php echo $expiry_date; ?>.
                  </td>
                </tr>
                <tr>
                  <td align="center" class="mobile"
                    style="font-family:roboto, helvetica, arial, sans-serif; font-size:14px; line-height:20px; color: #2e2f2f;">
                    Please contact us on <?php echo $phone_number; ?> or visit <a
                      href="<?php echo $site_url; ?>/membership">wfac.org.au/membership</a> to organise your renewal.
                  </td>
                </tr>
              </table>
              <!-- End Container -->
            </td>
          </tr>
          <tr>
            <td height="30" style="font-size:30px; line-height:30px;"> </td><!-- Spacer -->
          </tr>
        </table>
        <!-- End Header -->

        <!-- Footer -->
        <table width="600" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#27282d">
          <tr>
            <td height="30" style="font-size:40px; line-height:40px;"> </td><!-- Spacer -->
          </tr>
          <tr>
            <td align="center">

              <!-- Start Container -->
              <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                <tr>
                  <td width="560" class="mobile" style="font-size:12px; line-height:18px;" align="center">

                    <!-- Start Content -->
                    <table width="560" cellpadding="0" cellspacing="0" border="0" class="container" align="center">
                      <tr>
                        <td class="mobile" style="font-size:14px; line-height:20px;" align="center">

                          <!-- Start Button -->
                          <table width="75" cellpadding="0" cellspacing="0" align="center" border="0">
                            <tr>
                              <td align="center">

                                <!-- Start Container -->
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="color: #1e2532">
                                  <tr>
                                    <td width="35" height="25" style="" align="center" valign="middle">
                                      <a href="http://www.facebook.com/fremantleartscentre" target="_blank"
                                        style="text-decoration:none; height: 25px; outline: none; border: 0"><img
                                          src="<?php echo $template_directory_url; ?>/img/email/fb.png" alt=""
                                          style="display: block" border="0"></a>
                                    </td>
                                    <td width="35" height="25" style="" align="center" valign="middle">
                                      <a href="http://instagram.com/fremantleartscentre" target="_blank"
                                        style="text-decoration:none; height: 25px; outline: none; border: 0"><img
                                          src="<?php echo $template_directory_url; ?>/img/email/ig.png" alt=""
                                          style="display: block" border="0"></a>
                                    </td>
                                    <td width="35" height="25" style="" align="center" valign="middle">
                                      <a href="http://twitter.com/FreoArtsCentre" target="_blank"
                                        style="text-decoration:none; height: 25px; outline: none; border: 0"><img
                                          src="<?php echo $template_directory_url; ?>/img/email/tw.png" alt=""
                                          style="display: block" border="0"></a>
                                    </td>

                                  </tr>
                                </table>
                                <!-- Start Container -->

                              </td>
                            </tr>
                          </table>
                          <!-- End Button -->

                        </td>
                      </tr>
                      <tr>
                        <td height="15" style="font-size:15px; line-height:15px;" class="mobileOn"> </td><!-- Spacer -->
                      </tr>
                      <tr>
                        <td align="center" class="mobile"
                          style="font-family:roboto, helvetica, arial, sans-serif; font-size:12px; line-height:20px; color: #ffffff;">
                          <span style="color: #ffffff"><a href="http://fac.org.au"
                              style="color: #ffffff; text-decoration: underline" target="_blank">fac.org.au</a></span>
                        </td>
                      </tr>

                      <tr>
                        <td height="30" style="font-size:0; line-height:40px;"></td><!-- Spacer -->
                      </tr>
                    </table>
                    <!-- End Content -->

                  </td>
                </tr>
              </table>
              <!-- End Container -->

            </td>
          </tr>
        </table>
        <!-- Footer -->

      </td>
    </tr>
  </table>
  <!-- End Background -->

</body>

</html>