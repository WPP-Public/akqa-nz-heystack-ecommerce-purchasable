<?php

\Director::addRules(100, array(
    \EcommerceInputController::$url_segment . '//$Action/$ID' => 'EcommerceInputController'
));
