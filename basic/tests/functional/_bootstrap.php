<?php

new yii\web\Application(require(__DIR__ . '/_config.php'));

\Codeception\Util\Autoload::registerSuffix('Page', __DIR__.DIRECTORY_SEPARATOR.'_pages');