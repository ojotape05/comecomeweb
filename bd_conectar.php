<?php
	$connect = pg_connect("dbname=d1t7aqnqu21atk host=ec2-44-193-112-245.compute-1.amazonaws.com port=5432 user=gskexnztmsgotj password=f9afbb6a89c845ff5cd5f2e50f0e47a8f92bee24012f16b80b77f5a18d10feb6 sslmode=require");

	if (!$connect):
		echo "Falha na Conexão: ".pg_last_error();
	endif;
?>
