<?php

/*
+---------------------------------------------------------------------------+
| Openads v2.3                                                              |
| ============                                                              |
|                                                                           |
| Copyright (c) 2003-2007 Openads Limited                                   |
| For contact details, see: http://www.openads.org/                         |
|                                                                           |
| Copyright (c) 2000-2003 the phpAdsNew developers                          |
| For contact details, see: http://www.phpadsnew.com/                       |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id$
*/

/*-------------------------------------------------------*/
/* Return misc capabilities                              */
/*-------------------------------------------------------*/

function MAX_layerGetLimitations()
{
	$compatible = true;
	
    if (isset($GLOBALS['_MAX']['CLIENT'])) {
        $agent = $GLOBALS['_MAX']['CLIENT'];
    	
    	$compatible = $agent['browser'] == 'ie' && $agent['maj_ver'] < 5 ||
    				  $agent['browser'] == 'mz' && $agent['maj_ver'] < 1 ||
    				  $agent['browser'] == 'fx' && $agent['maj_ver'] < 1 ||
    				  $agent['browser'] == 'op' && $agent['maj_ver'] < 5 
    				  ? false : true;
	}
				  
	//$richmedia  = $agent['platform'] == 'Win' ? true : false;
	$richmedia = true;
	
	return array (
		'richmedia'  => $richmedia,
		'compatible' => $compatible
	);
}



/*-------------------------------------------------------*/
/* Output JS code for the layer                          */
/*-------------------------------------------------------*/

function MAX_layerPutJs($output, $uniqid)
{
	global $align, $collapsetime, $padding;
	
	// Register input variables
	MAX_commonRegisterGlobalsArray(array('align', 'collapsetime', 'padding'));
	
	
	// Calculate layer size (inc. borders)
	$layer_width = $output['width'] + 4 + $padding*2;
	$layer_height = $output['height'] + 30 + $padding*2;
	
?>

function MAX_findObj(n, d) { 
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
  d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i>d.layers.length;i++) x=MAX_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MAX_getClientSize() {
	if (window.innerHeight >= 0) {
		return [window.innerWidth, window.innerHeight];
	} else if (document.documentElement && document.documentElement.clientWidth > 0) {
		return [document.documentElement.clientWidth,document.documentElement.clientHeight]
	} else if (document.body.clientHeight > 0) {
		return [document.body.clientWidth,document.body.clientHeight]
	} else {
		return [0, 0]
	}
}

function MAX_adlayers_place_<?php echo $uniqid; ?>()
{
	var c = MAX_findObj('MAX_c<?php echo $uniqid; ?>');
	var o = MAX_findObj('MAX_o<?php echo $uniqid; ?>');

	if (!c || !o)
		return false;

	_s='style'
	
	var clientSize = MAX_getClientSize();
	if (document.all && !window.innerWidth) { 
<?php if ($align == 'left') { ?>
		c[_s].pixelLeft = 0;
		o[_s].pixelLeft = 0;
<?php } elseif ($align == 'center') { ?>
		c[_s].pixelLeft = (clientSize[0] - <?php echo $layer_width; ?>) / 2;
		o[_s].pixelLeft = (clientSize[0] - <?php echo $layer_width; ?>) / 2;
<?php } else { ?>
		c[_s].pixelLeft = clientSize[0] - <?php echo $layer_width; ?>;
		o[_s].pixelLeft = clientSize[0] - <?php echo $layer_width; ?>;
<?php } ?>
		c[_s].pixelTop = 0 + ( document.body.scrollTop || document.documentElement.scrollTop );
		o[_s].pixelTop = 0 + ( document.body.scrollTop || document.documentElement.scrollTop );
	} else {
<?php if ($align == 'left') { ?>
		c[_s].left = 0;
		o[_s].left = 0;
<?php } elseif ($align == 'center') { ?>
		c[_s].left = ( (clientSize[0] + window.pageXOffset - <?php echo $layer_width; ?>) / 2 ) + 'px';
		o[_s].left = ( (clientSize[0] + window.pageXOffset - <?php echo $layer_width; ?>) / 2 ) + 'px';
<?php } else { ?>
		c[_s].left = ( clientSize[0] + window.pageXOffset - <?php echo $layer_width; ?> - 16 ) + 'px';
		o[_s].left = ( clientSize[0] + window.pageXOffset - <?php echo $layer_width; ?> - 16 ) + 'px';
<?php } ?>
		c[_s].top = ( 0 + window.pageYOffset ) + 'px';
		o[_s].top = ( 0 + window.pageYOffset ) + 'px';
	}
}

function MAX_geopop(what, ad)
{
	var c = MAX_findObj('MAX_c' + ad);
	var o = MAX_findObj('MAX_o' + ad);

	if (!c || !o)
		return false;

	_s='style'
	_v='visibility'

	switch(what)
	{
		case 'collapse':
			c[_s][_v] = 'visible'; 
			o[_s][_v] = 'hidden';

			if (MAX_timerid[ad])
			{
				window.clearTimeout(MAX_timerid[ad]);
				MAX_timerid[ad] = false;
			}

			break;

		case 'expand':
			o[_s][_v] = 'visible';
			c[_s][_v] = 'hidden'; 

		break;

		case 'close':
			c[_s][_v] = 'hidden'; 
			o[_s][_v] = 'hidden';

		break;

		case 'open':
		
			MAX_adlayers_place_<?php echo $uniqid; ?>();

			c[_s][_v] = 'hidden';
			o[_s][_v] = 'visible';
<?php

if (isset($collapsetime) && $collapsetime > 0)
	echo "\t\t\treturn window.setTimeout('MAX_geopop(\\'collapse\\', \\'".$uniqid."\\')', ".($collapsetime * 1000).");";

?>

			break;
	}

	return false;
}


if (typeof MAX_timerid == 'undefined')
	MAX_timerid = new Array();

MAX_timerid['<?php echo $uniqid; ?>'] = MAX_geopop('open', '<?php echo $uniqid; ?>');

<?php
}



/*-------------------------------------------------------*/
/* Return HTML code for the layer                        */
/*-------------------------------------------------------*/

function MAX_layerGetHtml($output, $uniqid)
{
	global $target;
	global $align, $collapsetime, $padding, $closetext;
	
	$conf = $GLOBALS['_MAX']['CONF'];
	
	// Register input variables
	MAX_commonRegisterGlobalsArray(array('align', 'collapsetime', 'padding', 'closetext'));
	
	
	if (!isset($padding)) $padding = '2';
	
	// Calculate layer size (inc. borders)
	$layer_width = $output['width'] + 4 + $padding*2;
	$layer_height = $output['height'] + 30 + $padding*2;
	
	// Create imagepath
	$imagepath = 'http://' . $conf['webpath']['images'] . '/layerstyles/geocities/';
	
	// return HTML code
	return '
<div id="MAX_c'.$uniqid.'" style="position:absolute; width:'.$layer_width.'px; height:'.$layer_height.'px; z-index:98; left: 0px; top: 0px; visibility: hidden"> 
	<table width="100%" border="1" cellspacing="0" cellpadding="0" style="border-style: ridge; border-color: #ffffff">
		<tr>
			<td bordercolor="#DDDDDD" bgcolor="#000099" align="right" style="padding: 3px 3px 2px">' .
				'<img src="'.$imagepath.'expand.gif" alt="" style="width:12px;height:12px;margin: 0 3px;" onclick="MAX_geopop(\'expand\', \''.$uniqid.'\')" />' .
				'<img src="'.$imagepath.'close.gif" alt="" style="width:12px;height:12px;" onclick="MAX_geopop(\'close\', \''.$uniqid.'\')" />' .
			'</td>
		</tr>
'.(strlen($output['url']) && strlen($output['alt']) ?
'		<tr>
			<td bgcolor="#FFFFCC" align="center" style="font-family: Arial, helvetica, sans-serif; font-size: 11px; padding: 2px"><a href="'.$output['url'].'" '.(isset($target) ? 'target="'.$target.'"' : '').'style="color: #0000ff">'.$output['alt'].'</a></td>
		</tr>
' : '').
'	</table>
</div>
<div id="MAX_o'.$uniqid.'" style="position:absolute; width:'.$layer_width.'px; height:'.$layer_height.'px; z-index:99; left: 0px; top: 0px; visibility: hidden"> 
	<table width="100%" border="1" cellspacing="0" cellpadding="0" style="border-style: outset; border-color: #ffffff">
		<tr> 
			<td bordercolor="#DDDDDD" bgcolor="#000099" align="right" style="padding: 3px 3px 2px">' .
				'<img src="'.$imagepath.'expand-d.gif" alt="" style="width:12px;height:12px;margin: 0 3px;" />' .
				'<img src="'.$imagepath.'collapse.gif" alt="" style="width:12px;height:12px;" onclick="MAX_geopop(\'collapse\', \''.$uniqid.'\')" />' .
			'</td>
		</tr>
		<tr> 
			<td>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr> 
						<td align="center">
							<table border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
								<tr>
									<td width="'.$output['width'].'" height="'.$output['height'].'" align="center" valign="middle" style="padding: '.$padding.'px">'.$output['html'].'</td>
								</tr>
							</table>
						</td>
					</tr>'.(strlen($closetext) ? '
					<tr> 
						<td align="center" bgcolor="#FFFFFF" style="font-family: Arial, helvetica, sans-serif; font-size: 9px; padding: 1px">' .
							'<a href="#" onclick="phpAds_geopop(\'collapse\', \''.$uniqid.'\');return!1;" style="color:#0000ff">'.$closetext.'</a>' .
						'</td>
					</tr>' : '').'
				</table>
			</td>
		</tr>
	</table>
</div>
';
}

?>