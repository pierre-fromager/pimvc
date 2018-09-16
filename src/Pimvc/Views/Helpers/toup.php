<?php
error_reporting(E_ALL);
if (empty($argv[1])) {
    echo "phptoup - constant replacer PSR2 compliant \n"
    . "Usage : php ./toup.php /path/to/file.php\n";
} else {
    $filename = $argv[1];
    $inContent = file_get_contents($filename);
    $outContent = $inContent;
    $tokens = token_get_all($inContent, TOKEN_PARSE);
    $constTokenCount = 0;
    $space = ' ';
    $const = 'const' . $space;
    $self = 'self::';
    foreach ($tokens as $token) {
        if (!is_string($token)) {
            list($tokenType, $constName) = $token;
            switch ($tokenType) {
                case T_CONST:
                    $constTokenCount = 0;
                    break;
                default:
                    $constTokenCount++;
                    // wait space after const to get const name
                    if ($constTokenCount == 2) {
                        $up = strtoupper($constName);
                        $srchName = $const . $constName . $space;
                        $vNameRep = $const . $up . $space;
                        $srchSelf = $self . $constName;
                        $vSelfRep = $self . $up;
                        $outContent = str_replace($srchName, $vNameRep, $outContent);
                        $outContent = str_replace($srchSelf, $vSelfRep, $outContent);
                    }
                    break;
            }
        }
    }
    echo $outContent;
}