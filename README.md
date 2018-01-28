# PHP-fpcalc
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://travis-ci.org/mrfeathers/php-fpcalc.svg?branch=master)](https://travis-ci.org/mrfeathers/php-fpcalc)

This package is a wrapper for the [fpcalc command-line tool](https://acoustid.org/chromaprint). 

## Installation
First you need to get installed `fpcalc` on your system.

**OS X**
```bash
brew install chromaprint
```

**Ubuntu**
```bash
apt-get install libchromaprint-tools
```

**Arch Linux**
```bash
pacman -Sy chromaprint
```

Or you can get the latest version from [AcoustId site](https://acoustid.org/chromaprint)


Than you just need to require this package via [Composer](https://getcomposer.org/)
```bash
$ composer require mrfeathers/php-fpcalc
```

## Usage

Usage is pretty simple. First you create a `FpcalcProcess` using factory class.

```php
$factory = new FpcalcFactory();
$fpcalcProcess = $factory->create();
```

Than just call `generateFingerPrint` with array of file paths (or web stream uri - [more info](https://oxygene.sk/2016/12/chromaprint-1-4-released/))

```php
$result = $fpcalcProcess->generateFingerPrint(['myfile.mp3']);

//or you can generate fingerprints for more than one file
$result = $fpcalcProcess->generateFingerPrint(['myfile.mp3', 'mysecondfile.mp3']);


//using online stream radio
$result = $fpcalcProcess->generateFingerPrint(['http://icecast2.play.cz/radio1.mp3']);

```
As a result you'll get output string with generated fingerprint or fingerprints.

You're able to set some options:
- `format` - input format name
- `algorithm` - algorithm method (default 2). Available since fpcalc version 1.4.3
- `rate` - sample rate of the input
- `channels` - number of channels in the input audio
- `length` - restricts the duration of the processed input audio (default 120, in seconds)
- `chunk` - splits the input audio into chunks of $chunkDuration duration (in seconds)
- `overlap` - overlap the chunks slightly to make sure audio on the edge id fingeprinted
- `ts` - output UNIX timestamps for chunked results, useful when fingerprinting real-time audio stream
- `raw` - output fingerprints in the uncompressed format
- `outputFormat` - format of result output. Available: json, text, plain


> Sometimes fingerprint generation can be a long process, sou you can set the process timeout using `setTimeout` method (default is 60 seconds).

**Feel free to [open an issue](https://github.com/mrfeathers/php-fpcalc/issues/new) in case of bugs or improvement requests!**
