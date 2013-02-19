<?php

  // Config file. See config-sample.php
  require 'inc/config.php';

  // Facebook PHP SDK
  require 'inc/facebook-php-sdk/facebook.php';

  // New facebook object
  $facebook = new Facebook(array(
    'appId'  => APP_ID,
    'secret' => APP_SECRET,
  ));

  // Get Signed request
  $signedRequest = $facebook->getSignedRequest();

  // No signed request means that we are not on a facebook tab
  if (empty($signedRequest)) {
    // Exit, or redirect to facebook tab
    // Uncomment on production
    /*
    print "<script> top.location.href='" . APP_URL . "'</script>";
    exit;
    */
  }
  else {
    // Signed Request shortcut
    $like = $signedRequest['page']['liked'];
    $locale = $signedRequest['user']['locale'];
    if (empty($locale)) {
      $locale = DEFAULT_LOCALE;
    }
    
    if ($like) {
      // If the user 'like' the page, ask him to allow the app to access his informations
      if (!isset($signedRequest["user_id"])) {
        $query = array(
          'client_id' => APP_ID,
          'redirect_uri' => APP_URL,
          'scope' => 'email', // See http://developers.facebook.com/docs/reference/login/#permissions
        );
        $location = "https://www.facebook.com/dialog/oauth?" . http_build_query($query);
        print "<script> top.location.href='" . $location . "'</script>";
        exit;
      }
      else {
        // Get user id
        $user_id = $signedRequest['user_id'];

        // Get user profile
        $user_profile = $facebook->api('/me');

        // Available variables depend on 'scope'
        $email = $user_profile['email'];
        
      }
    }
    else {
      // User does not 'like' the page
    }

  }

?><!DOCTYPE html>
<html xmlns:fb="https://www.facebook.com/2008/fbml">
<head>
  <meta charset="utf-8">
  <title>FB Tab Boilerplate</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <?php if ($like == false): ?>
  <p>Please like our page !</p>
  <?php else: ?>
  <p>Thank you for liking our page !</p>
  <?php endif; ?>

  <!-- jQuery CDN & fallback -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="js/lib/jquery-1.6.2.min.js"><\/script>')</script>

  <!-- Custom js file -->
  <script src="js/main.js"></script>

  <!-- Facebook JS SDK -->
  <div id="fb-root"></div>
  <script>
    window.fbAsyncInit = function() {
      // Init FB App
      FB.init({
        appId: '<?php print APP_ID; ?>',
        cookie: true,
        xfbml: true,
        oauth: true
      });

      // Reload tab when user log in & out
      /*
      FB.Event.subscribe('auth.login', function(response) {
        window.location.reload();
      });
      FB.Event.subscribe('auth.logout', function(response) {
        window.location.reload();
      });
      */

      // Prevent iframe scrollbar
      window.setTimeout(function () {
          FB.Canvas.setAutoGrow()
      }, 250)
    };

    // Load facebook js file
    (function() {
      var e = document.createElement('script'); e.async = true;
      e.src = document.location.protocol +
        '//connect.facebook.net/<?php print $locale; ?>/all.js';
      document.getElementById('fb-root').appendChild(e);
    }());
  </script>
</body>
</html>
