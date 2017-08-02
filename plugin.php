<?php
/*
	Plugin Name: Go To Top
	Plugin URI: http://code.google.com/p/wordpress-go-to-top/
	Version: 0.0.8
	Author: dengtooling
	Author URI: http://code.google.com/p/wordpress-go-to-top/
	Description: Add a "Go to top" link to your posts, this is a wordpress plugin which base on	<a href="http://jquery.com/">jQuery</a>, <a href="http://gsgd.co.uk/sandbox/jquery/easing/">jQuery Easing</a> and <a href="http://blog.ph-creative.com/post/jquery-plugin-scroll-to-top-v3.aspx">Scroll to Top v3</a>
	*/

	/*
	Copyright 2010 dengtooling  (dengtooling@gmail.com)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details: http://www.gnu.org/licenses/gpl.txt
	*/

	// upon activation of the plugin, calls the Init function
	register_activation_hook(__FILE__, 'gttInit');
	// upon deactivation of the plugin, calls the Destroy function
	register_deactivation_hook(__FILE__, 'gttDestroy');
	// load plugin text domail
	load_plugin_textdomain('go-to-top', "/wp-content/plugins/go-to-top/language");
	
	// what to add in the header, calls the AddHeader function
	add_action('wp_head', 'gttAddHeader', 1);
	// what to add in the footer, calls the AddFooter function
	add_action('wp_footer', 'gttAddFooter', 1);
	// add the submenu in the admin menu panel
	add_action('admin_menu', 'gttAddAdminPanel');
	
	/**
	 * Called upon activation of the plugin. Sets some options.
	 */
	function gttInit(){
		add_option('gttText', 'Go To Top'); // the text
		add_option('gttSpeed', '1000'); // the background color
		add_option('gttStartPosition', '200'); // the foreground color
		add_option('gttEase', 'easeOutQuart'); // position
		add_option('gttButtonType', 'Normal-text'); // what should it look like
		add_option('gttShowPost', true); // whether to show in posts only
		add_option('gttEnable', true); // enable
	}

	/**
	 * Called upon deactivation of the plugin. Cleans our mess.
	 */
	function gttDestroy(){
		if(get_option('gttPurge')){
			delete_option('gttText');
			delete_option('gttSpeed');
			delete_option('gttStartPosition');
			delete_option('gttEase');
			delete_option('gttButtonType');
			delete_option('gttShowPost');
			delete_option('gttEnable');
		}
	}

	/**
	 * Add code to the header
	 */
	function gttAddHeader(){
		// Disable
		if(!get_option('gttEnable')){
			return ;
		}
		// Enable
		if(get_option('gttShowPost')){
			if(is_single()){
				gttAddMechanism();
			}
		} else{
			gttAddMechanism();
		}
	}

	/**
	 * Add code to the footer. It contains the HTML markup for the floating area
	 */
	function gttAddFooter(){
		// Disable
		if(!get_option('gttEnable')){
			return ;
		}
		// Enable
		if(get_option('gttShowPost')){
			if(is_single()){
				gttEchoHtml();
			}
		} else{
			gttEchoHtml();
		}
		echo '<script type="text/javascript">jQuery.noConflict();jQuery(function(){jQuery("#gtt_go-to-top a").scrollToTop({speed:' .get_option('gttSpeed'). ',ease:"' .get_option('gttEase'). '",start:' .get_option('gttStartPosition'). '});});</script>';
	}

	/**
	 * Add the submenu in the admin menu panel
	 */
	function gttAddAdminPanel(){
		add_options_page(__('Go To Top Administration', 'go-to-top'), __('Go To Top', 'go-to-top'), 'manage_options', __FILE__, 'gttAdmin');
	}
	
	/**
	 * Outputs the necessary code to implement the scrolling mechanism
	 */
	function gttAddMechanism(){
		wp_enqueue_script('jquery', WP_PLUGIN_URL . '/go-to-top/js/jquery-1.3.2.min.js');
		wp_enqueue_script('jquery-scroll', WP_PLUGIN_URL . '/go-to-top/js/jquery.scroll.min.js');
		wp_enqueue_script('jquery-easing', WP_PLUGIN_URL . '/go-to-top/js/jquery.easing.min.js');
		echo '<link rel="stylesheet" type="text/css" href="'. WP_PLUGIN_URL . '/go-to-top/css/style-'. strtolower(get_option('gttButtonType')) .'.css" />';
		echo '<!--[if IE 6]>
<link rel="stylesheet" type="text/css" href="'. WP_PLUGIN_URL . '/go-to-top/css/ie6.css" />
<![endif]-->';
	}
	
	/**
	 * Echo the HTML markup.
	 */
	 function gttEchoHtml(){
		 echo '<div id="gtt_go-to-top" class="gtt_wrapper"><a href="#" title="' . __('Go To Top', 'go-to-top').'"><span>';
		 echo get_option("gttText");
		 echo '</span></a></div>';
	 }
	 
	/**
	 * Checks the validity of a number
	 * @param $num Number
	 * @return boolean
	 */
	function validNum($num){
		if(preg_match('/^[0-9]+$/i', $num))
 			return true;
 		else
 			return false;
	}

	/**
	 * Outputs the HTML form for the admin area. Also updates the options.
	 */
	function gttAdmin(){
		if($_POST['action'] == 'save'){
			$ok = false;
			
			if($_POST['gttText']){
				update_option('gttText', $_POST['gttText']);
				$ok = true;
			}
			
			if($_POST['gttSpeed']){
				if(validNum($_POST['gttSpeed'])){
					update_option('gttSpeed', $_POST['gttSpeed']);
					$ok = true;
				}
			}
			
			if($_POST['gttStartPosition']){
				if(validNum($_POST['gttStartPosition'])){
					update_option('gttStartPosition', $_POST['gttStartPosition']);
					$ok = true;
				}
			}
			
			if($_POST['gttEase']){
				update_option('gttEase', $_POST['gttEase']);
				$ok = true;
			}
			
			if($_POST['gttButtonType']){
				update_option('gttButtonType', $_POST['gttButtonType']);
				$ok = true;
			}
			
			if($_POST['gttShowPost'] == 1){
				update_option('gttShowPost', true);
			}else{
				update_option('gttShowPost', false);
			}
			
			if($_POST['gttEnable'] == 1){
				update_option('gttEnable', true);
			}else{
				update_option('gttEnable', false);
			}
			
			if($_POST['gttPurge'] == 1){
				update_option('gttPurge', true);
			}else{
				update_option('gttPurge', false);
			}
			
			if($ok){
				?>
				<div id="message" class="updated fade">
					<p><?php echo __('Changes have been saved', 'go-to-top'); ?></p>
				</div>
				<?php 
			}else{
				?>
				<div id="message" class="error fade">
					<p><?php echo __('An error has occurred', 'go-to-top'); ?></p>
				</div>
				<?php 
			}
		}
		// Ease list
		$gttEaseList = array('swing', 'easeInQuad', 'easeOutQuad', 'easeInOutQuad', 
		'easeInCubic', 'easeOutCubic', 'easeInOutCubic', 'easeInQuart', 'easeOutQuart', 
		'easeInOutQuart', 'easeInQuint', 'easeOutQuint', 'easeInOutQuint', 'easeInSine', 
		'easeOutSine', 'easeInOutSine', 'easeInExpo', 'easeOutExpo', 'easeInOutExpo', 
		'easeInCirc', 'easeOutCirc', 'easeInOutCirc', 'easeInElastic', 'easeOutElastic', 
		'easeInOutElastic', 'easeInBack', 'easeOutBack', 'easeInOutBack', 'easeInBounce', 
		'easeOutBounce', 'easeInOutBounce');
		$gttButtonTypeList = array('Normal-text', 'Black-arrow');
		// get the options values
		$gttText = get_option('gttText');
		$gttSpeed = get_option('gttSpeed');
		$gttEase = get_option('gttEase');
		$gttButtonType = get_option('gttButtonType');
		$gttStartPosition = get_option('gttStartPosition');
		$gttShowPost = get_option('gttShowPost');
		$gttEnable = get_option('gttEnable');
		$gttPurge = get_option('gttPurge');
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div>
			<h2><?php echo __('Go To Top Settings', 'go-to-top'); ?></h2>
			<form method="post">
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="gttText"><?php echo __('Text to show', 'go-to-top'); ?></label>
						</th>
						<td>
							<input name="gttText" type="text" id="gttText" value="<?php echo $gttText;?>" class="regular-text code" />
							<span class="setting-description"><?php echo __('Default is', 'go-to-top'); ?> <code>Go to top</code></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="gttSpeed"><?php echo __('Scroll speed', 'go-to-top'); ?></label>
						</th>
						<td>
							<input name="gttSpeed" type="text" id="gttSpeed" value="<?php echo $gttSpeed;?>" class="regular-text code" />
							<span class="setting-description"><?php echo __('Default is', 'go-to-top'); ?> <code>1000</code>ms</span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="gttStartPosition"><?php echo __('Start position', 'go-to-top'); ?></label>
						</th>
						<td>
							<input name="gttStartPosition" type="text" id="gttStartPosition" value="<?php echo $gttStartPosition ;?>" class="regular-text code" />
							<span class="setting-description"><?php echo __('Default is', 'go-to-top'); ?>  <code>200</code>px</span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="gttEase"><?php echo __('Ease', 'go-to-top'); ?></label>
						</th>
						<td>
							<!--<input name="gttEase" type="text" id="gttEase" value="<?php echo $gttEase ;?>" class="regular-text code" />-->
                            <select name="gttEase">
                            <?php 
							foreach($gttEaseList as &$value){ ?>
                            	<option value="<?php echo $value ?>" <?php if($value==get_option('gttEase')){ echo 'selected="selected"'; } ?>><?php echo $value ?></option>
                            <?php } ?>
                            </select>
							<span class="setting-description"><?php echo __('Default is', 'go-to-top'); ?>  <code>easeOutQuart</code></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="gttButtonType"><?php echo __('Button Type', 'go-to-top'); ?></label>
						</th>
						<td>
						<?php 
						foreach($gttButtonTypeList as &$value){ ?>
							<label title="<?php echo $value ?>">
								<input type="radio" name="gttButtonType" value="<?php echo $value ?>" <?php if($value==get_option('gttButtonType')){ echo 'checked="checked"'; } ?>/>
								<?php if($value=='Normal-text'){ echo __($value, 'go-to-top'); }
								else { echo '<img src="'. WP_PLUGIN_URL . '/go-to-top/images/'. strtolower($value) .'.png" >';}?>
							</label><br />
						<?php } ?>
							
							<span class="setting-description"><?php echo __('Default is', 'go-to-top'); ?>  <code><?php echo __('Normal-text', 'go-to-top'); ?></code></span>
						</td>
					</tr>
					<tr>
						<th scope="row" class="th-full" align="left" colspan="2">
							<label for="gttShowPost">
								<input type="checkbox" id="gttShowPost" name="gttShowPost" value="1" <?php if($gttShowPost){ echo 'checked="checked"'; } ?> />
								<?php echo __('Show only in posts, not all pages', 'go-to-top'); ?>
							</label>
						</th>
					</tr>
                    <tr valign="top">
						<th scope="row" class="th-full" align="left" colspan="2">
							<label for="gttEnable">
								<input type="checkbox" id="gttEnable" name="gttEnable" value="1" <?php if($gttEnable){ echo 'checked="checked"'; } ?> />
								<?php echo __('Enable', 'go-to-top'); ?>
							</label>
						</th>
					</tr>
					<tr valign="top">
						<th scope="row" class="th-full" align="left" colspan="2">
							<label for="gttPurge">
								<input type="checkbox" id="gttPurge" name="gttPurge" value="1" <?php if($gttPurge){ echo 'checked="checked"'; } ?> />
								<?php echo __('Purge config when deactivated', 'go-to-top'); ?>
							</label>
						</th>
					</tr>
				</table>
				<p class="submit">
					<input type="hidden" name="action" value="save" />
					<input type="submit" name="Submit" class="button-primary" value="<?php echo __('Save Changes', 'go-to-top'); ?>" />
				</p>
			</form>
		</div>
		<?php 
	}
	
?>
