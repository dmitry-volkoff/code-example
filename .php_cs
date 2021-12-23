<?php

return PhpCsFixer\Config::create()
->setRiskyAllowed(true)
->setIndent("\t")
->setRules(array(
	'@PSR2' => true,
	'@PHP71Migration:risky' => true,
	'@PHP73Migration' => true,
	'indentation_type' => true,
//	'strict_param' => true,
	'array_syntax' => ['syntax' => 'short'],
	'braces' => array(
        	'position_after_anonymous_constructs' => 'next',
        	'position_after_control_structures' => 'next',
    )
))->setIndent("\t");