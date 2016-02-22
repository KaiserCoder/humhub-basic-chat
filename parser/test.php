<?php

use humhub\modules\ponychat\parser\PonyCode;

require 'PonyCode.php';

$code = new PonyCode();

echo $code->clean('[rainbow]salé[/rainbow]');
