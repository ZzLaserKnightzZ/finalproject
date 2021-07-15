<?php

//global program  $MAP_DATA;
if(!isset($MAP_DATA)) die();
//$MAP_DATA = 'botmap';
						$ar_map =[	['1'=>"sta:1",'txt'=>"sta:1",'x'=>0.15,'y'=>0.65],
                                    ['2'=>"sta:2",'txt'=>"sta:2",'x'=>0.15,'y'=>0.60],
									['3'=>"sta:3",'txt'=>"sta:3",'x'=>0.15,'y'=>0.40],
									['4'=>"sta:4",'txt'=>"sta:4",'x'=>0.15,'y'=>0.35],
                                    ['5'=>"re:1",'txt'=>"re:1",'x'=>0.25,'y'=>0.35],
                                    ['6'=>"re:2",'txt'=>"re:2",'x'=>0.35,'y'=>0.35],
                                    ['7'=>"sto:1",'txt'=>"sto:1",'x'=>0.45,'y'=>0.35],
                                    ['8'=>"sto:2",'txt'=>"sto:2",'x'=>0.55,'y'=>0.35],
                                    ['9'=>"sto:3",'txt'=>"sto:3",'x'=>0.65,'y'=>0.35],
                                    ['10'=>"sto:4",'txt'=>"sto:4",'x'=>0.75,'y'=>0.35],
                                    ['11'=>"sto:5",'txt'=>"sto:5",'x'=>0.85,'y'=>0.35],
                                    ['12'=>"sto:6",'txt'=>"sto:6",'x'=>0.95,'y'=>0.35],
									['13'=>"sto:7",'txt'=>"sto:7",'x'=>0.95,'y'=>0.45],
									['14'=>"sto:8",'txt'=>"sto:8",'x'=>0.95,'y'=>0.55],
                                    ['15'=>"sto:9",'txt'=>"sto:9",'x'=>0.95,'y'=>0.65],
                                    ['16'=>"sto:10",'txt'=>"sto:10",'x'=>0.85,'y'=>0.65],
                                    ['17'=>"sto:11",'txt'=>"sto:11",'x'=>0.75,'y'=>0.65],
                                    ['18'=>"sto:12",'txt'=>"sto:12",'x'=>0.65,'y'=>0.65],
                                    ['19'=>"sto:13",'txt'=>"sto:13",'x'=>0.55,'y'=>0.65],
									['20'=>"sto:14",'txt'=>"sto:14",'x'=>0.45,'y'=>0.65],
                                    ['21'=>"sen:1",'txt'=>"sen:1",'x'=>0.35,'y'=>0.65],
                                    ['22'=>"sen:2",'txt'=>"sen:2",'x'=>0.25,'y'=>0.65],
                                    ['23'=>"BOT1",'txt'=>"BOT1",'x'=>0.07,'y'=>0.40],
                                    ['24'=>"BOT2",'txt'=>"BOT2",'x'=>0.07,'y'=>0.60]
								];
								//var_dump($ar_map);
								$strOUT = '';
								static $LIST_POINT = array();  //$LIST_POINT = 
								if($MAP_DATA == 'jsmap'){
									foreach($ar_map as $val){
										$strOUT.='{txt:"'.$val["txt"].'",x:'.$val["x"].',y:'.$val["y"].'},';
									}
									$out = substr($strOUT,0,strlen($strOUT)-1);
									echo '<script> const direction = ['.$out.']; </script>';  //end crypt
								}else if($MAP_DATA == 'botmap'){
									for($i=1; $i <= count($ar_map) ; $i++){
										//echo $ar_map[$i-1][$i];
										array_push($LIST_POINT  , $ar_map[$i-1][$i.""]);
									}
									//var_dump($LIST_POINT);
								}

   


?>