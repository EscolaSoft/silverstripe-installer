<?php
/**
 * Created by PhpStorm.
 * User: qunabu
 * Date: 27.04.17
 * Time: 13:44
 */

function rcopy($src, $dest){

  // If source is not a directory stop processing
  if(!is_dir($src)) return false;

  // If the destination directory does not exist create it
  if(!is_dir($dest)) {
    if(!mkdir($dest)) {
      // If the destination directory could not be created stop processing
      return false;
    }
  }

  // Open the source directory to read in files
  $i = new DirectoryIterator($src);
  foreach($i as $f) {
    if($f->isFile()) {
      copy($f->getRealPath(), "$dest/" . $f->getFilename());
    } else if(!$f->isDot() && $f->isDir()) {
      rcopy($f->getRealPath(), "$dest/$f");
    }
  }
  return false;
}

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

function getCurrentDirectory() {
  $path = dirname(__FILE__);
  $position = strrpos($path,'/') + 1;
  return substr($path,$position);
}

$dir = dirname(__FILE__).'/themes/qunabu/.git';
echo $dir.' '.is_dir($dir);
echo "\n";
Delete($dir);

rcopy(dirname(__FILE__).'/themes/qunabu/', dirname(__FILE__).'/themes/'.strtolower(getCurrentDirectory()));