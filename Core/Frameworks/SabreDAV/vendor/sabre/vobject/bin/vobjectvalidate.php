#!/usr/bin/env php
<?php

namespace Sabre\VObject;

// This sucks.. we have to try to find the composer autoloader. But chances
// are, we can't find it this way. So we'll do our bestest
$paths = array(
    __DIR__ . '/../vendor/autoload.php',  // In case vobject is cloned directly
    __DIR__ . '/../../../autoload.php',   // In case vobject is a composer dependency.
);

foreach($paths as $path) {
    if (file_exists($path)) {
        include $path;
        break;
    }
}

if (!class_exists('Sabre\\VObject\\Version')) {
    fwrite(STDERR, "Composer autoloader could not be properly loaded.\n");
    die(1);
}

fwrite(STDERR, "SabreTooth VObject validator " . Version::VERSION . "\n");


$repair = false;
$posArgs = array();

// Argument parsing:
foreach($argv as $k=>$v) {

    if ($k===0) {
        continue;
    }
    if (substr($v,0,2)==='--') {
        switch($v) {
            case '--repair' :
                $repair = true;
                break;
            default :
                throw new InvalidArgumentException('Unknown option: ' . $v);
                break;
        }
        continue;
    }
    $posArgs[] = $v;

}

function help() {

    global $argv;

    fwrite(STDERR, <<<HELP
Usage instructions:

  {$argv[0]} [--repair] inputfile [outputfile]

  inputfile   Input .vcf or .ics file.
  outputfile  Output .vcf or .ics file. This is only used with --repair.
  --repair    Attempt to automatically repair broken files.

For both the output- and inputfile "-" can be specified, to use STDIN and STDOUT
respectively.

All other output from this script is sent to STDERR.

https://github.com/evert/sabre-vobject

HELP
);

}

if (count($posArgs) < 1) {
    help();
    die();
}

if ($posArgs[0]!=='-') {
    $input = fopen($posArgs[0],'r');
} else {
    $input = STDIN;
}

if (isset($posArgs[1]) && $posArgs[1]!=='-') {
    $output = fopen($posArgs[1],'w');
} else {
    $output = STDOUT;
}

// This is a bit of a hack to easily support multiple objects in a single file.
$inputStr = "BEGIN:X-SABRE-WRAPPER\r\n" . stream_get_contents($input);

$inputStr = rtrim($inputStr, "\r\n") . "\r\nEND:X-SABRE-WRAPPER\r\n";

// Now the actual work.
$vObj = Reader::read($inputStr);

$objects = $vObj->children();

foreach($objects as $child) {

    switch($child->name) {
        case 'VCALENDAR' :
            fwrite(STDERR, "iCalendar: " . (string)$child->VERSION . "\n");
            break;
        case 'VCARD' :
            fwrite(STDERR, "vCard: " . (string)$child->VERSION . "\n");
            break;
        default :
            fwrite(STDERR, "This was an unknown object, but it did parse. It's likely that validation will give you unexpected results.\n");
            break;
    }

    $options = 0;
    if ($repair) $options |= Node::REPAIR;

    $warnings = $child->validate($options);

    if (!count($warnings)) {
        fwrite(STDERR, "[GOOD NEWS] No warnings!\n");
    } else {
        foreach($warnings as $warn) {

            fwrite(STDERR, $warn['message'] . "\n");

        }

    }

    if ($repair) {
        fwrite($output, $child->serialize());
    }

}

