# modhub
Microservice to find the latest or latest (pre)release version of a mod hosted on GitHub

It will automatically redirect to the desired archive. Defaults to archived code of the latest proper release.

# Usage
Construct the desired url and follow it.

## Parameters
Base url: http://lynxlynx.info/ie/modhub.php (or wherever the file is hosted)
 * First parameter: ?githubName/modRepositoryName (eg. Gibberlings3/SongAndSilence)
 * All other parameters: &parameterName=value

Version parameters:
 * master: get the latest code
 * preonly: get the latest prerelease code
 * pre: get whichever (pre)release code is more recent
 * any: get the latest code from master if there is no release (does not support packages)

Package parameters (for mods that provide them):
 * pkg=win: windows exe package
 * pkg=wzp: windows zip package
 * pkg=lin: linux package
 * pkg=osx: apple package

Note that the pkg parameter does not work with master mode.

## Url examples
Spell Revisions latest release:
http://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions
 
Spell Revisions latest master commit (unpackaged):
http://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&master
 
Spell Revisions latest prerelease:
http://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&preonly
 
Spell Revisions latest release or prerelease, whichever is fresher:
http://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&pre

# Usage example

Show the most recent pre-release or release of SpellRevisions for OSX:

    $  curl -I "http://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&pkg=osx&pre"
    HTTP/1.1 302 Moved Temporarily
    Server: nginx
    Date: Fri, 14 Oct 2016 17:54:20 GMT
    Content-Type: text/html; charset=UTF-8
    Connection: keep-alive
    Location: https://github.com/Gibberlings3/SpellRevisions/releases/download/v4b13/osx-spell_rev-v4-beta13.tar.gz
    Vary: User-Agent

The URL printed under Location is the file that you want.

# For mod authors
If you want the package detection to work, make sure you attach packages to the Github release and mind these patterns:
 * a windows exe package name should end with "exe"
 * a windows zip package name should start with "win" and end with "zip"
 * a linux package name should start with "lin"
 * an osx package name should start with "osx"
 
For an example, check [a release](https://github.com/Gibberlings3/Tweaks-Anthology/releases/tag/Beta_5) of Tweaks-Anthology.
 
# For developers
If you want to deploy this somewhere, you'll have to get a Github token and insert it into the $auth variable.
