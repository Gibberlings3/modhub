<?php
// Uses latest github api - v3 at the time of writing

// WARNING: change the following line for the script to work (generate a token first!)
$auth = "github-username:token";

$params = array_keys($_GET);
$modpath = strip_tags(array_shift($params));
if (empty($modpath)) {
  echo "ERROR: bad input: " . $modpath . " from " . array_keys(strip_tags($_GET));
  exit(12);
}

function getData($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, 'IE mod link fetcher');
  curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Accept: application/vnd.github.v3+json"));
  curl_setopt($ch, CURLOPT_USERPWD, $auth);
  curl_setopt($ch, CURLOPT_VERBOSE, true);
  $data = curl_exec($ch);
  if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch) . "\n... while trying to fetch $url\n";
  }
  curl_close($ch);
  return $data;
}

function redirect($url) {
  Header("Location: " . $url);
  exit();
}

// if needed, retry with _ -> ., since php doesn't allow them
// triggered by eg. GraionDilach/Neverwinter-Nights-Voice-Pack-for-EE-2.6
function unfsckdot($url, $json) {
  if (!empty($json["message"])) {
    $url = str_replace("_", ".", $url);
    $json = json_decode(getData($url), TRUE);
  }
  return $json;
}

$prefix = "https://github.com/" . $modpath;
$apiprefix = "https://api.github.com/repos/";
$suffix = "/releases";
$url = $apiprefix . $modpath . $suffix;
$prerelease = -1;
if (in_array("master", $params)) {
  // just the master zipball
  $url = $prefix . "/archive/master.zip";
  redirect($url);
} else if (count($params) == 0 || (count($params) == 1 && (in_array("pkg", $params) || in_array("ifeellucky", $params)))) {
  if (in_array("pkg", $params)) {
    // latest proper release, package
    $prerelease = 0;
  } else {
    // latest proper release, source
    $url = $url . "/latest";
    $json = json_decode(getData($url), TRUE);
    $json = unfsckdot($url, $json);
    if (!empty($json["message"])) {
      if (in_array("ifeellucky", $params)) {
        $url = $prefix . "/archive/master.zip";
        redirect($url);
      } else {
        echo "Mod probably has no releases yet, bailing out!\n";
        exit(13);
      }
    }
    redirect($json['zipball_url']);
  }
}

if (in_array("preonly", $params)) {
  $prerelease = 2;
} else if ($prerelease < 0) { // promoted as "pre", but any garbage would do
  // whichever is latest - release or prerelease
  $prerelease = 1;
}

// only got here if there were other parameters (possibly invalid)
// now to distinguish latest prerelease and latest (pre)release (use either) and handle packages
$json = json_decode(getData($url), TRUE);
$releases = unfsckdot($url, $json);

$osregex = array("lin" => "/^lin-/i", "osx" => "/^(osx|mac)-/i", "mac" => "/^(osx|mac)-/i", "win" => "/\.exe$/i", "wzp" => "/^win-.*\.zip$/i", "zip" => "/^(?!win-|osx-|lin-|wzp-).*\.zip$/i", "iemod" => "/\.iemod$/i");
foreach ($releases as $release) {
  //print_r($release);
  if ($release["prerelease"] != 1 && $prerelease == 2) {
    // skip proper releases
    continue;
  }
  if ($release["prerelease"] == 1 && $prerelease == 0) {
    // use proper release, packaged
    continue;
  }
  // otherwise return the first thing
  // WARNING: ASSUMING FIRST AVAILABLE THING IS LATEST
  if (in_array("pkg", $params)) {
    // platform specific package download, eg.:
    // /releases/download/v4b12/spell_rev-v4-beta12.exe
    $version = $release['tag_name'];
    // support a csv list, trying each package in turn
    $pkgs = explode(",", $_GET["pkg"]);
    foreach ($pkgs as $os) {
      foreach ($release["assets"] as $package) {
        if (preg_match($osregex[$os], $package["name"])) {
          redirect($package["browser_download_url"]);
        }
      }
    }
  } else {
    // grab the source
    redirect($release['zipball_url']);
  }

  // mark error if nothing was found
  http_response_code(404);
}

?>
