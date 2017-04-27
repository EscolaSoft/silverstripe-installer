<?php
/**
 * Created by PhpStorm.
 * User: qunabu
 * Date: 27.04.17
 * Time: 13:44
 */

function Delete($path)
{
  if (is_dir($path) === true)
  {
    $files = array_diff(scandir($path), array('.', '..'));

    foreach ($files as $file)
    {
      Delete(realpath($path) . '/' . $file);
    }

    return rmdir($path);
  }

  else if (is_file($path) === true)
  {
    return unlink($path);
  }

  return false;
}

$dir = dirname(__FILE__).'/themes/qunabu/.git';
echo $dir.' '.is_dir($dir);
Delete($dir);

//Delete();