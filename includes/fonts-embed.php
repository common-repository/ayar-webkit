<?php

	//global $awk_options, $embeded_fonts;
	$embeded_fonts=array();	
	//$awk_options = get_option( 'awk_options' );
	$ayar = ($awk_options['ayar'] ? 'ayar':'');
	if ($ayar == "ayar"){
	$embeded_fonts[0]= "ayar";
	}

	$ayar_takhu = ($awk_options['ayar_takhu'] ? 'ayar_takhu':'');
	if ($ayar_takhu == "ayar_takhu"){
	$embeded_fonts[1]= "ayar_takhu";
	}

	$ayar_kasone = ($awk_options['ayar_kasone'] ? 'ayar_kasone':'');
	if ($ayar_kasone == "ayar_kasone"){
	$embeded_fonts[2]= "ayar_kasone";
	}

	$ayar_nayon = ($awk_options['ayar_nayon'] ? 'ayar_nayon':'');
	if ($ayar_nayon == "ayar_nayon"){
	$embeded_fonts[3]= "ayar_nayon";
	}
	
	$ayar_wazo = ($awk_options['ayar_wazo'] ? 'ayar_wazo':'');
	if ($ayar_wazo == "ayar_wazo"){
	$embeded_fonts[4]= "ayar_wazo";
	}
	
	$ayar_wagaung = ($awk_options['ayar_wagaung'] ? 'ayar_wagaung':'');
	if ($ayar_wagaung == "ayar_wagaung"){
	$embeded_fonts[5]= "ayar_wagaung";
	}
	
	$ayar_tawthalin = ($awk_options['ayar_tawthalin'] ? 'ayar_tawthalin':'');
	if ($ayar_tawthalin == "ayar_tawthalin"){
	$embeded_fonts[6]= "ayar_tawthalin";
	}
	
	$ayar_thadingyut = ($awk_options['ayar_thadingyut'] ? 'ayar_thadingyut':'');
	if ($ayar_thadingyut == "thadingyut"){
	$embeded_fonts[7]= "ayar_thadingyut";
	}
	
	$ayar_tazaungmone = ($awk_options['ayar_tazaungmone'] ? 'ayar_tazaungmone':'');
	if ($ayar_tazaungmone == "ayar_tazaungmone"){
	$embeded_fonts[8]= "ayar_tazaungmone";
	}
	
	$ayar_natdaw = ($awk_options['ayar_natdaw'] ? 'ayar_natdaw':'');
	if ($ayar_natdaw == "ayar_natdaw"){
	$embeded_fonts[9]= "ayar_natdaw";
	}
	
	$ayar_pyatho = ($awk_options['ayar_pyatho'] ? 'ayar_pyatho':'');
	if ($ayar_pyatho == "ayar_pyatho"){
	$embeded_fonts[10]= "ayar_pyatho";
	}	
	
	$ayar_tapotwe = ($awk_options['ayar_tapotwe'] ? 'ayar_tapotwe':'');
	if ($ayar_tapotwe == "ayar_tapotwe"){
	$embeded_fonts[11]= "ayar_tapotwe";
	}

	$ayar_tabaung = ($awk_options['ayar_tabaung'] ? 'ayar_tabaung':'');
	if ($ayar_tabaung == "ayar_tabaung"){
	$embeded_fonts[12]= "ayar_tabaung";
	}
	
	$ayar_thawka = ($awk_options['ayar_thawka'] ? 'ayar_thawka':'');
	if ($ayar_thawka == "ayar_thawka"){
	$embeded_fonts[13]= "ayar_thawka";
	}
	
	$ayar_juno = ($awk_options['ayar_juno'] ? 'ayar_juno':'');
	if ($ayar_juno == "ayar_juno"){
	$embeded_fonts[14]= "ayar_juno";
	}
	
	$ayar_type = ($awk_options['ayar_typewriter'] ? 'ayar_typewriter':'');
	if ($ayar_type == "ayar_typewriter"){
	$embeded_fonts[15]= "ayar_typewriter";
	}
	
	$myanmar3 = ($awk_options['myanmar3'] ? 'myanmar3':'');
	if ($myanmar3 == "myanmar3"){
	$embeded_fonts[16]= "myanmar3";
	}
	
	$padauk = ($awk_options['padauk'] ? 'padauk':'');
	if ($padauk == "padauk"){
	$embeded_fonts[17]= "padauk";
	}
	
	$parabaik = ($awk_options['parabaik'] ? 'parapaik':'');
	if ($parabaik == "parabaik"){
	$embeded_fonts[18]= "parabaik";
	}
	
	$mymyanmar = ($awk_options['mymyanmar'] ? 'mymyanmar':'');
	if ($myamyanmar == "mymyanmar"){
	$embeded_fonts[19]= "mymyanmar";
	}
	
	$masterpiece = ($awk_options['masterpiece'] ? 'masterpiece':'');
	if ($masterpiece == "masterpiece"){
	$embeded_fonts[20]= "masterpiece";
	}
	
	$yunghkio = ($awk_options['yunghkio'] ? 'yunghkio':'');
	if ($yunghkio == "yunghkio"){
	$embeded_fonts[21]= "yunghkio";
	}
	
	$zawgyi = ($awk_options['zawgyi'] ? 'zawgyi':'');
	if ($zawgyi == "zawgyi"){
	$embeded_fonts[22]= "zawgyi-one";
	}

	$embeded_fonts = implode(",",$embeded_fonts);
	


		//$awk_options = get_option( 'awk_options' );
		//$fonts_server = $awk_options['fonts_server_url'];
		if ($fonts_server == 'other'){	
			$fonts_server_url = $awk_options['other_server_url'].'css/';
		}elseif ($fonts_server == 'same_domain'){
			$fonts_server_url = AWK_PLUGIN_URL.'/includes/web-fonts.php';
		}elseif ($fonts_server == 'default'){
			$fonts_server_url = 'http://webfont.myanmapress.com/css/';
			}

		$handle = 'awk_fonts_embed';
		$src = $fonts_server_url.'?font='.$embeded_fonts;
		$deps = false;
		$ver = false;
		$media = false;
		wp_register_style( $handle, $src, $deps, $ver, $media );
		//$mobile = ($awk_options['mobile_enable'] ? 'yes':'');
if ($mobile != 'yes'){
 wp_enqueue_style( $handle );  }elseif ($mobile == 'yes' && $browser->isMobile() == false){  wp_enqueue_style( $handle );  }else{ return; }

?>
