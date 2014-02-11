<?php

if (basename(dirname(__FILE__)) != 'editlock') {
	throw new Exception('The edit lock module is not installed in correct directory. The directory should be named "editlock"');
}