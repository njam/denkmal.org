<?php

$searchCli = new CM_Elasticsearch_Index_Cli(null, new CM_OutputStream_Stream_Output());
$searchCli->create('song');
$searchCli->create('event');
