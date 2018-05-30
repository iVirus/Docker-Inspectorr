<?php
require_once('inc/auth.class.php');

$auth = new Auth();

if ($auth->isConfigured() && !$auth->isValidSession()) {
  header('Location: login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>Inspectorr</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
    <link rel='stylesheet' href='//bootswatch.com/4/darkly/bootstrap.min.css'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.0.12/css/all.css' integrity='sha384-G0fIWCsCzJIMAVNQPfjH08cyYaUtMwjJwqiRKxxE/rx96Uroj1BtIQ6MLJuheaO9' crossorigin='anonymous'>
    <link rel='stylesheet' href='css/main.css'>
  </head>
  <body>
    <div class='container'>
      <div id='messages' class='my-3'>
      </div>
      <div class='card border-secondary my-3'>
        <div class='card-header'>
          <ul class='nav nav-tabs card-header-tabs float-left'>
            <li class='nav-item'>
              <h4 class='mb-0 cursor-default'><a class='nav-link border-bottom-0 px-3 custom-tab active' onclick='void(0)' data-tab='indexStatus'><span class='fa fa-check'></span><span class='d-none d-md-inline ml-2'>Index Status</span></a></h4>
            </li>
            <li class='nav-item'>
              <h4 class='mb-0 cursor-default'><a class='nav-link border-bottom-0 px-3 custom-tab' onclick='void(0)' data-tab='audioQuality'><span class='fa fa-headphones'></span><span class='d-none d-md-inline ml-2'>Audio Quality</span></a></h4>
            </li>
            <li class='nav-item'>
              <h4 class='mb-0 cursor-default'><a class='nav-link border-bottom-0 px-3 custom-tab' onclick='void(0)' data-tab='videoQuality'><span class='fa fa-video'></span><span class='d-none d-md-inline ml-2'>Video Quality</span></a></h4>
            </li>
          </ul>
          <h2 class='mb-0'><span id='libraries-loading' class='fa fa-sync text-muted float-right refresh-libraries cursor-pointer' title='Refresh libraries' onclick='void(0)'></span></h2>
        </div>
        <div id='libraries' class='card-body'>
        </div>
        <div id='statuses' class='card-footer'>
<?php
if (!$auth->isConfigured()) {
  echo "          <span class='float-right'><a class='text-white' href='/auth.php'><span class='fa fa-lock-open' title='Configure Authentication'></span></a></span>" . PHP_EOL;
} else {
  echo "          <span class='float-right'><a class='text-white' href='/logout.php'><span class='fa fa-sign-out-alt' title='Log out'></span></a></span>" . PHP_EOL;
}
?>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.2.1.min.js' integrity='sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
    <script src='js/complet.class.js'></script>
    <script src='js/complet.js'></script>
  </body>
</html>
