<?php

//list all domains for translation

function pg_get_domains($echo = 1)
{
	$txtd = array(
				'general'=>'Allgemein',
				'art'=>'Kunst- und Geisteswissenschaften, Gesellschaftswissenschaften',
				'law'=>'Recht und Verwaltung',
				'sci'=>'Naturwissenschaften',
				'tech'=>'Technologie / Technik, Ingenieurwesen',
				'info'=>'Informationstechnologie / -technik',
				'tel'=>'Telekommunikation und Medien',
				'bio'=>'Gesundheit, Biologie, Medizin',
				'eco'=>'Wirtschaft und Handel',
				'agr'=>'Landwirtschaft',
				'travel'=>'Reisen, Transportwesen',
				'mil'=>'Milit&auml;r',
				'sport'=>'Sport, Freizeit'
			);
			
	if (1 == $echo)
		foreach ($txtd as $k => $v) 
			echo "<option value='$k'>$v</option>";
	else
		return $txtd;
}