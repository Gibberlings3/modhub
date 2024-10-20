# modhub
Microservice to find the latest release, prerelease, or latest commit of a mod hosted on GitHub.

It will automatically redirect to the desired archive. Defaults to archived code of the latest proper release.

# Usage
Construct the desired URL and open it with a browser or other tool.

## Parameters
Base url: `https://lynxlynx.info/ie/modhub.php` (or wherever the file is hosted)
 * first parameter: `?githubName/modRepositoryName` (eg. Gibberlings3/SongAndSilence)
 * all other parameters: `&parameterName=value`

Version parameters:
 * `master`: get the latest code
 * `preonly`: get the latest prerelease code
 * `pre`: get whichever release or prerelease code is more recent
 * `ifeellucky`: get the latest code, master if there is no release (does not support packages)

Package parameters (for mods that provide them):
 * `pkg=iemod`: Universal iemod package, works for Windows, macOS, Linux
 * `pkg=zip`: Universal zip package, works for Windows, macOS, Linux
 * `pkg=win`: Windows exe package
 * `pkg=wzp`: Windows zip package
 * `pkg=mac` or `pkg=osx`: macOS package
 * `pkg=lin`: Linux package

Note that the pkg parameter does not work with master mode. You can provide a comma separated list
and packages will be tried in turn until one is found, e.g. `pkg=win,wzp,zip`.

## Url examples
Spell Revisions latest release:  
http://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions

Spell Revisions latest prerelease:  
https://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&preonly

Spell Revisions latest release or prerelease, whichever of them is newer:  
https://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&pre

Spell Revisions latest master commit (unpackaged):  
https://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&master

Spell Revisions latest release or prerelease, if there is no release or prerelease then fallback to the last commit from master branch:  
https://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&ifeellucky

Spell Revisions windows zip package from latest release:
https://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&pkg=win

Spell Revisions macOS zip package from latest prerelease:
https://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&pkg=osx&preonly

Spell Revisions Linux zip package from latest release or prerelease, whichever of them is newer:
https://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&pkg=lin&pre

ModPackage example mod universal iemod package from latest release:
https://lynxlynx.info/ie/modhub.php?InfinityMods/ModPackage&pkg=iemod

ModPackage example mod universal zip package from latest release:
https://lynxlynx.info/ie/modhub.php?InfinityMods/ModPackage&pkg=zip

**WARNING**: if there are multiple zip files in the release (excluding those with 'win-', 'osx-', 'lin-' prefixes), the 'zip' fetching behavior is undefined.

BGT Tweak Pack windows exe or windows zip package if the exe isn't found, both from latest release:
https://lynxlynx.info/ie/modhub.php?Spellhold-Studios/BGT-Tweak-Pack&pkg=win,wzp

# Usage example

Get the link to the most recent macOS (pre-)release package of SpellRevisions:

    curl -I "https://lynxlynx.info/ie/modhub.php?Gibberlings3/SpellRevisions&pkg=osx&pre"
    HTTP/1.1 302 Moved Temporarily
    Date: Tue, 25 Jun 2019 06:48:56 GMT
    Server: Apache
    Upgrade: h2,h2c
    Connection: Upgrade
    Location: https://github.com/Gibberlings3/SpellRevisions/releases/download/v4b16/osx-spell_rev-v4-beta16.zip
    Vary: User-Agent
    Content-Type: text/html; charset=UTF-8

The URL printed under Location is the file that you want.

# For mod authors
If you want the package detection to work, make sure you attach packages to the Github release and mind these patterns:
 * universal iemod package name should end with ".iemod"
 * universal zip package name should end with ".zip" and it should be the only non-prefixed (see below) zip file present
 * windows exe package name should end with ".exe"
 * windows zip package name should start with "win-" and end with ".zip"
 * macOS package name should start with "osx-"
 * linux package name should start with "lin-"

For a real life example, check a [release](https://github.com/Gibberlings3/Tweaks-Anthology/releases) of Tweaks Anthology.
 
# For developers
If you want to deploy this somewhere, you'll have to get a Github token and insert it into the $auth variable.
