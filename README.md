# WaldoBehatExtension

A perceptual diff extension for [Behat](http://behat.org/) to highlight **visual regressions** in web applications.

Was written as a client for [Waldo](https://github.com/AndreasHeiberg/Waldo) visual regression testing server, but can be used with multiple drivers including a local filesystem driver that saves screenshots to your disk.

Simply put after each of your existing Behat steps have run a screenshot will be taken and compared to the baseline. If the two differ your test will fail on the basis of visual regression.

Images of with the eact difference of the two screenshots will be saved for you to examine.

## Requirements
This package will only work with Mink Drivers that support screenshotting. You can not use it with Goutte, BrowserKit, etc. as these drivers while fast don't render the page.

Any Selenium2 driver should work perfectly, Firefox, Chrome, PhantomJS, etc.

If you're using the filesystem driver you will also need [ImageMagick installed and enabled in PHP](http://php.net/manual/en/imagick.setup.php).

## Installation
Download the package with composer:

    composer require -dev andheiberg/waldo-behat-extension
    
    // if you want to use a Waldo server you need Guzzle
    composer require guzzlehttp/guzzle
    
    // if you want to use the Filesystem drivers you need Flystem for storage and Intervention for image comparisons
    composer require league/flysystem
    composer require intervention/image

Enable the behat extention:

    // behat.yml
    default:
        extensions:
            AndHeiberg\WaldoBehatExtension: ~


## Configuration            

### Filesystem
By default the extension will use the filesystem drivers and save screenshots in `/waldo` at the root of your project.

You can change this easily. `root` let's you set the directory to save screenshots in relative to the root of your project. You can also change local file storage to any of the Flysystem adapters including S3.

`fail_on_diff` allows you to remove assertions for visual regressions and instead manually look at the diffs at your leasure.

    // behat.yml
    default:
        extensions:
            AndHeiberg\WaldoBehatExtension:
                filesystem:
                    driver: local
                    root: /testing_output/waldo
                fail_on_diff: false

### Waldo Server
The extension get's a whole lot more interesting when combined with a Waldo server. Consider the case of testing a staging server before you flip it for production. SSH'ing into a box does not make for an intuative way to see the visual diffs on failure. A waldo server solves this by giving you a nice GUI for inspecting visual regression testing results.

    default:
        extensions:
            AndHeiberg\WaldoBehatExtension:
                screenshotter:
                    driver: waldo_server
                    server: http://localhost:8000
                screenshot_comparer:
                    driver: waldo_server
                    server: http://localhost:8000

## Test it
Clone this repo:

    git clone git@github.com:AndreasHeiberg/WaldoBehatExtension.git
    cd WaldoBehatExtension

Edit `src/AndHeiberg/WaldoBehatExtension/Waldo.php`:

    require_once __DIR__.'/../../../vendor/autoload.php';
    require_once __DIR__.'/../../../vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

Run behat:

    vendor/bin/phantomjs --webdriver=4444
    vendor/bin/behat

## TODO

- [ ] Better support for multiple screensizes and browsers
- [ ] Deal with dynamic content
  - [ ] Probably gonna add hooks for running js before taking the screenshot.

## Inspiration

* [Brett Slatkin](http://github.com/bslatkin) for his brilliant presentation on how they use perceptual diffs at Google.
* [Huxley](http://github.com/facebook/huxley)
* [BackstopJS](https://github.com/garris/BackstopJS)
* [PhantomCSS](https://github.com/Huddle/PhantomCSS)
* [Wraith](https://github.com/BBC-News/wraith)
* [Shoov](http://shoov.io/)