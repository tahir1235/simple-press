<?php
/*
Simple:Press
Admin plugins list
$LastChangedDate: 2017-12-28 11:37:41 -0600 (Thu, 28 Dec 2017) $
$Rev: 15601 $
*/

if ( preg_match( '#' . basename( __FILE__ ) . '#', $_SERVER['PHP_SELF'] ) ) {
	die( 'Access denied - you cannot directly call this file' );
}

$search_term = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';

?>

<form action="<?php echo SPADMINPLUGINS; ?>" method="post" id="sppluginssearchform" name="sppluginssearchform">
		<?php echo sp_create_nonce( 'forum-adminform_plugins' ); ?>
		<input type="hidden" name="s" value="<?php echo $search_term; ?>" />

</form>
<?php



function spa_plugins_list_form() {
	?>
	<script>
		(function (spj, $, undefined) {
			$(document).ready(function () {

				$('.search-box input[type="search"]').keyup( function(e) {
					if( e.which == 13 ) {
						$('select[name^="action"]').val('-1');

						$('#sppluginssearchform input[type=hidden][name=s]').val($(this).val());
						$('#sppluginssearchform').submit();
					}
				});
				
				spj.loadAjaxForm('sppluginsform', 'sfreloadpl');
				/* wp check all logic */
				$('thead, tfoot').find('.check-column :checkbox').click(function (e) {

					var c = $(this).prop('checked'),
						kbtoggle = 'undefined' == typeof toggleWithKeyboard ? false : toggleWithKeyboard,
						toggle = e.shiftKey || kbtoggle;

					$(this).closest('table').children('tbody').filter(':visible')
						.children().children('.check-column').find(':checkbox')
						.prop('checked', function () {
							if ($(this).is(':hidden'))
								return false;
							if (toggle)
								return $(this).prop('checked');
							else if (c)
								return true;
							return false;
						});

					$(this).closest('table').children('thead,  tfoot').filter(':visible')
						.children().children('.check-column').find(':checkbox')
						.prop('checked', function () {
							if (toggle)
								return false;
							else if (c)
								return true;
							return false;
						});
				});
				
				
				$('.column-more img').click(function (e) {
					if ($(this).parent().find('.sp-plugin-more').css('display') === 'none') {
						$(this).parent().find('.sp-plugin-more').css('display', 'block');
					} else {
						$(this).parent().find('.sp-plugin-more').css('display', 'none');
					}
				});
				
			});

			

			function display_filtr() {
				if ($('#sf-plugins-flt-b').css('display') === 'none') {
					$('#sf-plugins-flt-b').css('display', 'block');
				} else {
					$('#sf-plugins-flt-b').css('display', 'none');
				}
			}

			$('#sf-plugins-flt-t').click(display_filtr);
		}(window.spj = window.spj || {}, jQuery));
	</script>

	<?php
	// get plugins
	$plugins          = spa_get_plugins_list_data();
	$plugins_active   = 0;
	$plugins_inactive = 0;
	foreach ( (array) $plugins as $plugin_file => $plugin_data ) {
		$is_active = SP()->plugin->is_active( $plugin_file );
		$path      = explode( '/', $plugin_file );
		$bad       = is_numeric( substr( $path[0], - 1 ) );
		if ( $is_active ) {
			$plugins_active ++;
		} else {
			if ( $bad ) {

			} else {
				$plugins_inactive ++;
			}
		}
	}
	// get update version info
	$xml = sp_load_version_xml();

	// get versions of our plugins
	$versions = array();
	$required = array();
	$folders  = array();
	if ( $xml ) {
		foreach ( $xml->plugins->plugin as $plugin ) {
			$versions[ (string) $plugin->name ] = (string) $plugin->version;
			$required[ (string) $plugin->name ] = (string) $plugin->requires;
			$folders[ (string) $plugin->name ]  = (string) $plugin->folder;
		}
	}

	// check active plugins
	$invalid = SP()->plugin->validate_active();
	if ( ! empty( $invalid ) ) {
		foreach ( $invalid as $plugin_file => $error ) {
			echo '<div id="message" class="error"><p>' . sprintf( SP()->primitives->admin_text( 'The plugin %1$s has been deactivated due to error: %2$s' ), esc_html( $plugin_file ), $error->get_error_message() ) . '</p></div>';
		}
	}
	$ajaxURL = wp_nonce_url( SPAJAXURL . 'plugins-loader&amp;saveform=list', 'plugins-loader' );
	$msg     = esc_js( SP()->primitives->admin_text( 'Are you sure you want to delete the selected Simple Press plugins?' ) );
	?>

	<style>
		.sf-plugin-list-mob {
			text-align: left;
			margin-top: 20px;
			background-color: white;
		}
		.sf-plugin-list-mob {
			content: '' !important;
		}
		.sf-plugin-list-mob td {
			padding: 10px;
		}
		.sf-button-primary a {
			color: #FFFFFF;
			text-align: center;
		}
		.sf-title-uppercase-blue {
			color: #85A2BC;
			text-transform: uppercase;
		}
		.sf-plugins-uninstall {
			width: 120%;
			height: 50px;
			background-color: #EAF0F4;
			/*opacity: 0.1;*/
		}
		.sf-plugins-uninstall-child {
			padding-top: 15px !important;
			text-align: center;
		}
		.sf-plugins-uninstall a {
			color: #006992;
		}
		.sf-label-select-all {
			float: left;
			margin-bottom: 20px;
		}
		#doActionRight {
			font-weight: bold !important;
			font-size: 30px !important;
			letter-spacing: -5px !important;
			word-spacing: 0 !important;
			display: inline-block;
			float: left;
			width: auto;
			height: 42px;
			padding: 12px 18px 11px 18px;
			margin-top: 3px;
			line-height: 17px;
			background: #00B9D0;
			box-shadow: 0 3px 6px rgba(0, 105, 146, .1);
			border: none;
			font-family: "Open Sans Semibold";
			font-size: 12px;
			color: #FFF;
			text-shadow: none;
			text-align: center;
			text-decoration: none;
			outline: none;
			white-space: nowrap;
			vertical-align: top;
			cursor: pointer;
			-webkit-user-select: none; /* Safari 3.1+ */
			-moz-user-select: none; /* Firefox 2+ */
			-ms-user-select: none; /* IE 10+ */
			user-select: none; /* Standard syntax */
		}
		#sf-plugins-flt-t{
			color: #006992;
		}
	</style>

	<form action="<?php echo $ajaxURL; ?>" method="post" id="sppluginsform" name="sppluginsform"
		  onsubmit="javascript: if (ActionType.options[ActionType.selectedIndex].value == 'delete-selected' || ActionType2.options[ActionType2.selectedIndex].value == 'delete-selected') {if (confirm('<?php echo $msg; ?>')) {return true;} else {return false;}} else {return true;}">
		<?php echo sp_create_nonce( 'forum-adminform_plugins' ); ?>
		<?php
		spa_paint_options_init();
		spa_paint_open_tab( SP()->primitives->admin_text( 'Available Plugins' ), true );
		spa_paint_open_panel();

		spa_paint_spacer();

		$strspace    = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$stroutname0 = SP()->primitives->admin_text( 'All ' ) . '(' . count( $plugins ) . ')';
		$strout0     = "<a href='" . esc_url( add_query_arg( 'plugingroup', 'all', SPADMINPLUGINS ) ) . "'>$stroutname0</a>" . $strspace;
		$stroutname  = SP()->primitives->admin_text( 'Active ' ) . '(' . $plugins_active . ')';
		$strout1     = "<a href='" . esc_url( add_query_arg( 'plugingroup', 'active', SPADMINPLUGINS ) ) . "'>$stroutname</a>" . $strspace;
		$stroutname  = SP()->primitives->admin_text( 'Inactive' ) . '(' . $plugins_inactive . ')';
		$strout2     = "<a href='" . esc_url( add_query_arg( 'plugingroup', 'inactive', SPADMINPLUGINS ) ) . "'>$stroutname</a>" . $strspace;
		$strout      = $strout0 . $strout1 . $strout2;
		// start panel actions
		spa_paint_open_panel();
		?>

		<fieldset class="sf-fieldset">
			<div class="sf-show sf-plugins-flt">
				<div id="sf-plugins-flt-t"><?php echo $stroutname0; ?></div>
				<div id="sf-plugins-flt-b">
					<div><?php echo $strout0; ?></div>
					<div><?php echo $strout1; ?></div>
					<div><?php echo $strout2; ?></div>
				</div>
			</div>
			<div class="sf-panel-body-top">
				<div class="sf-panel-body-top-left sf-plugin-hide">
					<?php
					// display view links
					echo $strout;
					?>
				</div>

				<div class="sf-panel-body-top-right sf-plugin-hide">
					<p class="search-box">
						<input type="search" id="<?php echo esc_attr( 'search_id-search-input' ); ?>" name="s" value="<?php _admin_search_query(); ?>" form="plugin-filter"
							   placeholder="<?php echo SP()->primitives->admin_text( 'Search plugins' ); ?>"/>
					</p>
					<?php echo spa_paint_help( 'plugins' ); ?>
				</div>

				<div class="sf-panel-body-top-left-midle sf-plugin-hide">
					<div class="sf-actions-in">
						<div class="sf-actions">
							<select id="ActionType" name="action1">
								<option selected="selected" value="-1"><?php echo SP()->primitives->admin_text( 'Bulk Actions' ); ?></option>
								<option value="activate-selected"><?php echo SP()->primitives->admin_text( 'Activate' ); ?></option>
								<option value="deactivate-selected"><?php echo SP()->primitives->admin_text( 'Deactivate' ); ?></option>
								<?php if ( ! is_multisite() || is_super_admin() ) { ?>
									<option value="delete-selected"><?php echo SP()->primitives->admin_text( 'Uninstall' ); ?></option><?php } ?>
							</select>
						</div>
						<span class="sf-action-button"><input id="doaction1" class="sf-action-button-b" type="submit" value="-&#8250;"/></span>
					</div>
				</div>

				<div class="sf-panel-body-top-right sf-showm sf-width-100-per">
					<p class="search-box">
						<input type="search" style="width:100%" id="<?php echo esc_attr( 'search_id-search-input' ); ?>" name="s" value="<?php _admin_search_query(); ?>" form="plugin-filter"
							   placeholder="<?php echo SP()->primitives->admin_text( 'Search plugins' ); ?>"/>
					</p>
					<div class="sf-pt-15">
						<?php
						echo spa_paint_help( 'plugins' );
						?>
					</div>
				</div>

			</div>
		</fieldset>
		<?php
		// end panel actions
		spa_paint_close_panel();
		?>

		<div class="sf-plugin-hide">

			<table class="wp-list-table widefat plugins">
				<thead>
				<tr class="sf-plugin-hide">
					<td id='cb' class='manage-column column-cb check-column'>
						&nbsp;&nbsp;&nbsp;<input type="checkbox" name="cbhead" id="cbhead"/>
						<label class="wp-core-ui" for='cbhead'>&nbsp;</label>
					</td>
					<th class='manage-column check-column'></th>
					<th class='manage-column column-name column-primary'>
						<?php SP()->primitives->admin_etext( 'Plugin' ); ?>
					</th>
					<th class='manage-column column-description'>
						<?php SP()->primitives->admin_etext( 'Description' ); ?>
					</th>
					<th class='manage-column column-vertion'>
						<?php SP()->primitives->admin_etext( 'Version' ); ?>
					</th>
					<th class='manage-column column-act'></th>
					<th class='manage-column column-more'></th>
				</tr>
				</thead>
				<tbody class="the-list">

				<?php
				if ( empty( $plugins ) ) {
					echo '<tr><td colspan="7">' . SP()->primitives->admin_text( 'No plugins found.' ) . '</td></tr>';
				}

				$disabled = '';

				foreach ( (array) $plugins as $plugin_file => $plugin_data ) {
					$update = ( ! empty( $versions[ $plugin_data['Name'] ] ) ) ? ( ( version_compare( $versions[ $plugin_data['Name'] ], $plugin_data['Version'], '>' ) == 1 ) ) : 0;
					// check for valid folder name
					$path = explode( '/', $plugin_file );
					$bad  = is_numeric( substr( $path[0], - 1 ) );

					$is_active = SP()->plugin->is_active( $plugin_file );
					if ( isset( $_REQUEST['s'] ) && strlen( trim( $_REQUEST['s'] ) ) && strripos( $plugin_data['Name'], $_REQUEST['s'] ) === false ) {
						continue;
					}
					// Search by name
					if ( $is_active ) {
						if ( isset( $_REQUEST['plugingroup'] ) && trim( $_REQUEST['plugingroup'] ) === 'inactive' ) {
							continue;
						} //filter by inactive
						$url            = SPADMINPLUGINS . '&amp;action=deactivate&amp;plugin=' . esc_attr( $plugin_file ) . '&amp;title=' . urlencode( esc_attr( $plugin_data['Name'] ) ) . '&amp;sfnonce=' . wp_create_nonce( 'forum-adminform_plugins' );
						$actionlink     = "<a href='$url' title='" . SP()->primitives->admin_text( 'Deactivate this Plugin' ) . "'>"
										  . SP()->primitives->admin_text( 'Deactivate' ) . '</a>';
						$actionlink_mob = $actionlink;
						// $actionlink     = apply_filters( 'sph_plugins_active_buttons', $actionlink, $plugin_file );
						// $actionlink .= sp_paint_plugin_tip( $plugin_data['Name'], $plugin_file );
						$rowClass = 'active';
						if ( $update ) {
							$rowClass .= ' update';
						}
						$icon = '<span class="sf-icon sf-check" title="' . SP()->primitives->admin_text( 'Plugin activated' ) . '"></span>';
					} else {
						if ( $bad ) {
							$rowClass   = 'inactive spWarningBG';
							$actionlink = '';
							$icon       = '<img src="' . SPADMINIMAGES . 'sp_NoWrite.png" title="' . SP()->primitives->admin_text( 'Warning' ) . '" alt="" style="vertical-align:middle;" />';
							$disabled   = ' disabled="disabled" ';
						} else {
							if ( isset( $_REQUEST['plugingroup'] ) && trim( $_REQUEST['plugingroup'] ) === 'active' ) {
								continue;
							} //filter by active
							$url            = SPADMINPLUGINS . '&amp;action=activate&amp;plugin=' . esc_attr( $plugin_file ) . '&amp;title=' . urlencode( esc_attr( $plugin_data['Name'] ) ) . '&amp;sfnonce=' . wp_create_nonce( 'forum-adminform_plugins' );
							$actionlink     = "<a href='$url' title='" . SP()->primitives->admin_text( 'Activate this Plugin' ) . "'>"
											  . SP()->primitives->admin_text( 'Activate' ) . '</a>';
							$actionlink_mob = $actionlink;
							$url            = SPADMINPLUGINS . '&amp;action=delete&amp;plugin=' . esc_attr( $plugin_file ) . '&amp;title=' . urlencode( esc_attr( $plugin_data['Name'] ) ) . '&amp;sfnonce=' . wp_create_nonce( 'forum-adminform_plugins' );
							$msg            = esc_js( SP()->primitives->admin_text( 'Are you sure you want to delete this Simple Press plugin?' ) );

							$actionlink = apply_filters( 'sph_plugins_inactive_buttons', $actionlink, $plugin_file );
							$rowClass   = 'inactive';
							if ( $update ) {
								$rowClass .= ' update';
							}
							$icon     = '<span class="sf-icon sf-no-check" title="' . SP()->primitives->admin_text( 'Plugin not activated' ) . '"></span>';
							$disabled = '';
						}
					}

					$description = $plugin_data['Description'];
					$plugin_name = $plugin_data['Name'];
					?>
					<tr class='<?php echo $rowClass; ?>'>
						<th class='manage-column column-cb check-column' scope='row'>
							<?php
							$thisId = 'checkbox_' . rand();
							?>
							&nbsp;&nbsp;&nbsp;
							<?php
							$checkbox                      = '<input type="checkbox" value="' . $plugin_file . '" name="checked[]" ' . $disabled . '/>';
							$mobile[ $thisId ]['checkbox'] = $checkbox;
							?>
							<?php echo $checkbox; ?>
						</th>
						<td class='manage-column check-column'>
							<?php $mobile[ $thisId ]['icon'] = $icon; ?>
							<?php echo $icon; ?>
						</td>
						<td class='manage-column column-name column-primary'>
							<strong>
								<?php $mobile[ $thisId ]['plugin_name'] = esc_html( $plugin_name ); ?>
								<?php echo esc_html( $plugin_name ); ?>
							</strong>
							<!--<div class="row-actions-visible">
						   <span><?php echo str_replace( '&nbsp;&nbsp;', '  |  ', $actionlink ); ?></span>
					</div>-->
						</td>
						<td class='manage-column column-description'>
							<div class='manage-column column-description'>
								<?php
								$mobile[ $thisId ]['description'] = $description;
								// Remove any "simple:press" or "simplepress" references from the description if running a white label installation.
								if ( spa_white_label_check() ) {
									$description = str_replace( 'Simple:Press', SP()->primitives->admin_text( 'Forum' ), $description );
								}
								// Now show the description
								echo $description;
								?>
									
								<?php
								if ( ! empty( $plugin_data['Author'] ) ) {
									$author = $plugin_data['Author'];

									if ( ! empty( $plugin_data['AuthorURI'] ) ) {
										$author = '<a href="' . esc_url( $plugin_data['AuthorURI'] ) . '" title="' . SP()->primitives->admin_text( 'Visit author homepage' ) . '">' . esc_html( $plugin_data['Author'] ) . '</a>';
									}

									if ( ! spa_white_label_check() && ! spa_saas_check() ) {
										echo '<div class="plugin-description-author">' . sprintf( SP()->primitives->admin_text( 'By %s' ), $author ) . '</div>';
									}
								}

								?>
									
									
									
							</div>
							<div class='<?php echo $rowClass; ?> second plugin-version-author-uri'>

								<?php
								$plugin_meta = array();

								if ( $is_active ) {
									$plugin_meta[] = sp_paint_plugin_tip( $plugin_data['Name'], $plugin_file );

									$plugin_meta = array_merge( $plugin_meta, sp_active_plugin_options( $plugin_file ) );

								}

								if ( ! empty( $plugin_data['Version'] ) ) {
									$plugin_version = sprintf( SP()->primitives->admin_text( '%s' ), $plugin_data['Version'] );
								}

								if ( ! empty( $plugin_data['PluginURI'] ) ) {
									$plugin_meta[] = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . SP()->primitives->admin_text( 'Visit plugin site' ) . '">' . esc_html( SP()->primitives->admin_text( 'Visit plugin site' ) ) . '</a>';
								}
								if ( ! is_multisite() || is_super_admin() ) {

									$uninstall = '<a href="javascript: if (confirm(\'' . $msg . '\')) {window.location=\'' . $url . '\';}" title="' . SP()->primitives->admin_text( 'Uninstall this Plugin' ) . '">' . SP()->primitives->admin_text( 'Uninstall' ) . '</a>';

									$plugin_meta[]                  = $uninstall;
									$mobile[ $thisId ]['uninstall'] = $uninstall;
								}
								?>
							</div>
						</td>
						<td class='manage-column column-vertion'>
							<?php $mobile[ $thisId ]['plugin_version'] = $plugin_version; ?>
							<?php echo $plugin_version; ?>
						</td>
						<td class='manage-column column-act'>
							<?php $mobile[ $thisId ]['actionlink'] = $actionlink_mob; ?>
							<?php echo $actionlink; ?>
						</td>
						<td class='manage-column column-more'>
							<img src="<?php echo SP_PLUGIN_ICONS; ?>More.svg" alt=""/>
							<div class="sp-plugin-more"><?php echo implode( ' | ', $plugin_meta ); ?></div>
						</td>
					</tr>
					<?php
					// is it bad?
					if ( $bad ) {
						preg_match( '/-?\s*\d+$/', $path[0], $fix );
						$suggest = str_replace( $fix[0], '', $path[0] );
						?>
						<tr class='<?php echo $rowClass; ?>'>
							<td colspan="7">
								<div class="sf-alert-block sf-info">
									<?php echo sprintf( SP()->primitives->admin_text( 'The folder name of this plugin has become corrupted - probably due to multiple downloads. Please remove the %s at the end of the folder name.  The proper folder name should be %s' ), "<strong>$fix[0]</strong>", "<strong>$suggest</strong>" ); ?>
								</div>
							</td>
						</tr>
						<?php
					}

					if ( isset( $plugin_data['ItemId'] ) && $plugin_data['ItemId'] != '' ) {

						// any upgrade for this plugin using licensing method

						$sp_plugin_name = sanitize_title_with_dashes( $plugin_data['Name'] );

						$check_for_addon_update = SP()->options->get( 'spl_plugin_versioninfo_' . $plugin_data['ItemId'] );
						$check_for_addon_update = json_decode( $check_for_addon_update );

						$check_addons_status = SP()->options->get( 'spl_plugin_info_' . $plugin_data['ItemId'] );
						$check_addons_status = json_decode( $check_addons_status );

						$update_condition = $check_for_addon_update != '' && isset( $check_for_addon_update->new_version ) && $check_for_addon_update->new_version != false;
						$status_condition = $check_addons_status != '' && isset( $check_addons_status->license );

						$version_compare = isset( $check_for_addon_update->new_version ) && ( version_compare( $check_for_addon_update->new_version, $plugin_data['Version'], '>' ) == 1 );

						if ( is_main_site() && $update_condition && $status_condition && $version_compare ) {

							$changelog_link = add_query_arg(
								array(
									'tab'       => 'plugin-information',
									'plugin'    => $sp_plugin_name,
									'section'   => 'changelog',
									'TB_iframe' => true,
									'width'     => 722,
									'height'    => 949,
								),
								admin_url( 'plugin-install.php' )
							);

							$ajaxURThem = wp_nonce_url( SPAJAXURL . 'license-check', 'license-check' );

							?>
							<tr class='<?php echo $rowClass; ?>'>
								<td></td>
								<td class="plugin-update colspanchange" colspan="3">
									<div class="update-message notice inline notice-warning notice-alt">
										<?php
										if ( $check_addons_status->license == 'valid' ) {

											echo SP()->primitives->admin_text( 'There is an update for the ' ) . ' ' . $plugin_data['Name'] . ' '
												 . SP()->primitives->admin_text( 'plugin' ) . '.<br />';
											echo SP()->primitives->admin_text( 'Version' ) . ' ' . $check_for_addon_update->new_version . ' '
												 . SP()->primitives->admin_text( 'of the plugin is available' ) . '.<br />';
											echo '<span title="' . SP()->primitives->admin_text( 'View version full details' ) . '" class="thickbox open-plugin-details-modal spPluginUpdate" data-width="1000" data-height="0" data-site="' . $ajaxURThem . '" data-label="Simple:Press Plugin Update" data-href="' . esc_url( $changelog_link ) . '">' . SP()->primitives->admin_text( 'View version ' ) . $check_for_addon_update->new_version
												 . SP()->primitives->admin_text( ' details ' ) . '</span> '
												 . SP()->primitives->admin_text( 'or' ) . ' ';
											echo '<a href="' . self_admin_url( 'update-core.php' ) . '" title="'
												 . SP()->primitives->admin_text( 'update now' ) . '">' . SP()->primitives->admin_text( 'update now' ) . '</a>.';

										} else {

											echo SP()->primitives->admin_text( 'There is an update for the ' ) . ' ' . $plugin_data['Name'] . ' ' . SP()->primitives->admin_text( 'plugin' ) . '.<br />';
											echo SP()->primitives->admin_text( 'Version' ) . ' ' . $check_for_addon_update->new_version . ' '
												 . SP()->primitives->admin_text( 'of the plugin is available' ) . '.<br />';
											echo '<span title="' . SP()->primitives->admin_text( 'View version full details.' ) . '" class="thickbox open-plugin-details-modal spPluginUpdate" data-width="1000" data-height="0" data-site="' . $ajaxURThem . '" data-label="Simple:Press Plugin Update" data-href="' . esc_url( $changelog_link ) . '">' . SP()->primitives->admin_text( 'View version ' ) . $check_for_addon_update->new_version . SP()->primitives->admin_text( ' details.' ) . '</span>';
											echo '<br />' . SP()->primitives->admin_text( ' Automatic update is unavailable for this plugin - most likely because the license key is not present and activated.' );
										}
										?>
									</div>
								</td>
							</tr>
							<?php
						}
					} else {

						// any upgrade for this plugin?  in multisite only main site can update
						if ( is_main_site() && $versions && $update ) {
							$active = ( $is_active ) ? ' active' : '';
							?>
							<tr class="plugin-update-tr<?php echo $active; ?>">
								<td class="plugin-update colspanchange" colspan="4">
									<div class="update-message notice inline notice-warning notice-alt">
										<?php echo SP()->primitives->admin_text( 'There is an update for the' ) . ' ' . $plugin_data['Name'] . ' ' . SP()->primitives->admin_text( 'plugin' ) . '.<br />'; ?>
										<?php echo SP()->primitives->admin_text( 'Version' ) . ' ' . $versions[ $plugin_data['Name'] ] . ' ' . SP()->primitives->admin_text( 'of the plugin is available' ) . '.<br />'; ?>
										<?php echo SP()->primitives->admin_text( 'This newer version requires at least Simple:Press version' ) . ' ' . $required[ $plugin_data['Name'] ] . '.<br />'; ?>
										<?php echo SP()->primitives->admin_text( 'For details, please visit' ) . ' ' . SPPLUGHOME . ' ' . SP()->primitives->admin_text( 'or' ) . ' '; ?>
										<?php echo '<a href="' . self_admin_url( 'update-core.php' ) . '" title="Simple:Press Plugin Update">' . SP()->primitives->admin_text( 'update now' ) . '</a>.'; ?>
									</div>
								</td>
							</tr>
							<?php
						}
					}
				}
				do_action( 'sph_plugins_list_panel' );
				?>
				</tbody>

				<tfoot>
				<tr>
					<td class='manage-column column-cb check-column'>
						&nbsp;&nbsp;&nbsp;<input type="checkbox" name="cbfoot" id="cbfoot"/>
						<label class="wp-core-ui" for='cbfoot'>&nbsp;</label>
					</td>
					<th class='manage-column check-column'></th>
					<th class='manage-column column-name column-primary'>
						<?php SP()->primitives->admin_etext( 'Plugin' ); ?>
					</th>
					<th class='manage-column column-description'>
						<?php SP()->primitives->admin_etext( 'Description' ); ?>
					</th>
				</tr>
				</tfoot>
			</table>
		</div>

		<div class="sf-showm">
			<input type="checkbox" name="cbhead" id="cbhead_mob"/>
			<label class="wp-core-ui sf-label-select-all" for='cbhead_mob'>SELECT ALL</label>

			<?php if ( isset( $mobile ) && is_array( $mobile ) ) : ?>
				<?php foreach ( $mobile as $item ) : ?>
					<table class="sf-plugin-list-mob">
						<tr>
							<td><?php echo $item['checkbox']; ?></td>
							<td align="right"><?php echo $item['icon']; ?></td>
						</tr>
						<tr>
							<td><span class="sf-title-uppercase-blue">PLUGIN</span></td>
							<td><span class="sf-title-uppercase-blue">VERSION</span></td>
						</tr>
						<tr>
							<td><?php echo $item['plugin_name']; ?></td>
							<td><?php echo $item['plugin_version']; ?></td>
						</tr>
						<tr>
							<td colspan="2"><span class="sf-title-uppercase-blue">DESCRIPTION</span></td>
						</tr>
						<tr>
							<td colspan="2"><?php echo $item['description']; ?></td>
						</tr>
						<tr>
							<td>
								<div class="sf-plugins-uninstall">
									<div class="sf-plugins-uninstall-child">
										<?php
										if ( $item['uninstall'] ) {
											echo $item['uninstall'];
										}
										?>
									</div>
								</div>
							</td>

							<td>
							<span class="sf-button-primary">
								<?php
								if ( $item['actionlink'] ) {
									echo $item['actionlink'];
								}
								?>
							</span>
							</td>
						</tr>
					</table>
					<hr>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<?php
		spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
		?>
		<div class="sfform-panel-spacer"></div>
		<?php
		spa_paint_close_tab();
		?>
	</form>
	<?php
}

function sp_paint_plugin_tip( $name, $file ) {
	// make sure getting started help file name replacement works or bail to keep from returning the main plugin file
	if ( $file == ( $xfile = str_replace( '-plugin.php', '-help.php', $file ) ) ) {
		return '';
	}
	// if help file does not exist dont show the help option.
	$path = SP_STORE_DIR . '/' . SP()->plugin->storage['plugins'] . '/' . $xfile;
	if ( ! realpath( $path ) ) {
		return '';
	}
	$site   = wp_nonce_url( SPAJAXURL . "plugin-tip&amp;file=$xfile", 'plugin-tip' );
	$atitle = SP()->primitives->admin_text( 'Getting Started' );
	$htitle = $atitle . ' - ' . esc_js( $name );
	$out    = '<br />';
	$out   .= '<a class="spLinkHighlight spOpenDialog" data-site="' . $site . '" data-label="' . $htitle . '" data-width="400" data-height="0" data-align="center"><b>' . $atitle . '</b></a>';

	return $out;
}


/**
 * Return plugin related options except uninstall
 *
 * @param string $plugin_file
 */
function sp_active_plugin_options( $plugin_file ) {

	$plugin_options = explode( '&nbsp;&nbsp;', apply_filters( 'sph_plugins_active_buttons', '', $plugin_file ) );

	$plugin_options = array_filter( $plugin_options );

	foreach ( $plugin_options as $po_id => $po_tag ) {

		preg_match( '~>\K[^<>]*(?=<)~', $po_tag, $match );

		if ( strtolower( $match[0] ) === 'uninstall' ) {
			unset( $plugin_options[ $po_id ] );
		}
	}

	return $plugin_options;
}
