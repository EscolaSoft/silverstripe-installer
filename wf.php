<?php
/**
 * Created by PhpStorm.
 * User: qunabu
 * Date: 11.08.17
 * Time: 18:09
 */

if (PHP_SAPI === 'cli') {

} else {
  die('nope');
}

include(__DIR__ . '/vendor/autoload.php');

use FortyTwoStudio\WebFactionPHP\WebFactionClient;
use FortyTwoStudio\WebFactionPHP\WebFactionException;


    try
    {


      $login = readline("login: ");
      readline_add_history($login);

      $password = readline("password: ");
      readline_add_history($password);

      $projectname = readline("project name: ");
      readline_add_history($projectname);


      if ($login && $password && $projectname) {
        echo "logging in.... please wait \n ";
      } else {
        die ('must provide  $login && $password && $projectname'."\n");
      }


      // create a connection to the API, use your own credentials here, obvs
      $wf = new WebFactionClient($login, $password);

      $ip = '185.119.174.181';

      $apps = $wf->listApps();

      $exsists = false;
      foreach($apps as $app) {
        if ($app['name'] == $projectname) {
          $exsists = true;
        }
      }

      $db_pass = WebFactionClient::generatePassword(21);


      if ($exsists) {
        echo "PROJECT exisits:\n";
        $wf->changeDbUserPassword($projectname, $db_pass, 'mysql');
      } else {


        $app = $wf->createApp($projectname,'static_php56');

        $domain = $wf->createDomain($projectname.'.qunabu.com');

        $website = $wf->createWebsite($projectname, '185.119.174.181', FALSE, [$projectname . '.qunabu.com'], [
            $projectname,
            '/'
          ]);

        $user = $wf->createDbUser($projectname,$db_pass,'mysql');

        // https://docs.webfaction.com/xmlrpc-api/apiref.html#method-create_db
        $database = $wf->createDb($projectname, 'mysql', '', $projectname);

        // https://docs.webfaction.com/xmlrpc-api/apiref.html#method-change_db_user_password
        //otherwise it doesn't seem to use it. Possibly because we're creating the user at the same time as the DB above

      }


      echo "PROJECT DETAILS:\n";
      echo "projectname: $projectname \n";
      echo "application static_php56 name : $projectname \n";
      echo "domain : $projectname.qunabu.com \n";
      echo "mysql host : localhost \n";
      echo "mysql username : $projectname \n";
      echo "mysql database : $projectname \n";
      echo "mysql passwrod : $db_pass \n";
      echo "ip: $ip \n";
      echo "path: /home/qunabu/webapps/$projectname \n";


    } catch (WebFactionException $e)
    {
      // Something went wrong, find out what with $e->getMessage() but be warned, WebFaction exception messages are often
      // vague and unhelpful!
      echo "rut roh, this went wrong: " . $e->getMessage() . "\n\n";
    }





