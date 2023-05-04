<?php
/**
* @version 			SEBLOD Importer 1.x
* @package			SEBLOD Importer Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JPluginHelper::importPlugin( 'cck_storage_location' );
JText::script( 'COM_CCK_CONFIRM_PURGE_OUTPUT_FOLDER' );

$app		=	JFactory::getApplication();
$config		=	JCckDev::init( array(), true );
$params		=	JComponentHelper::getParams( 'com_cck_importer' );
$session	=	JFactory::getSession();
$ajax		=	$params->get( 'mode', 0 );
$ajax_load	=	'components/com_cck/assets/styles/seblod/images/ajax.gif';
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
JFactory::getDocument()->addStyleDeclaration( 'div.seblod .adminformlist button {margin:0;} div.seblod .adminformlist-2cols li {margin:0;} #system-message-container.j-toggle-main.span10{width: 100%;} .seblod-fix{padding-left:0!important; padding-right:0!important;} .seblod-fix ul{margin-left:0;} .seblod-fix table td{vertical-align:middle;} .column-name.excluded{text-decoration:line-through;} .column-name.selected{font-weight:bold;} li .icon-help.hasTooltip{margin:12px 0 0 2px;} .info-raw.disabled{padding-left:8px;}' );

$ajaxStep	=	0;
$ajaxTotal	=	0;
$do			=	$app->input->get( 'do', '' );
$js			=	'';
$wait		=	0;
if ( $do == 'ok' ) {
	$res	=	$session->get( 'cck_importer_batch_ok' );
	if ( isset( $res['message'] ) && $res['message'] ) {
		$app->enqueueMessage( $res['message'], $res['message_type'] );
	}
} elseif ( $do && $session->get( 'cck_importer_batch' ) ) {
	$ajaxStep	=	$params->get( 'mode_ajax_count', 25 );
	$ajaxTotal	=	(int)$do;

	if ( $ajaxTotal ) {
		$session_data	=	$session->get( 'cck_importer_batch' );

		if ( isset( $session_data['options']['workflow'] ) && $session_data['options']['workflow'] ) {
			$wait	=	1;
		}
	}
} else {
	$session->set( 'cck_importer_batch', '' );
}
$session->set( 'cck_importer_batch_ok', '' );

require_once JPATH_ADMINISTRATOR.'/components/com_cck_importer/helpers/helper_import.php';
?>

<form enctype="multipart/form-data" action="<?php echo JRoute::_( 'index.php?option=' . $this->option ); ?>" method="post" id="adminForm" name="adminForm">
<div>

<div class="<?php echo $this->css['wrapper']; ?> hidden-phone">
	<div class="<?php echo $this->css['w100']; ?>">
		<div class="seblod first cpanel_news full">
			<div class="legend top center plus"><?php echo CCK_LABEL .' &rarr; '. JText::_( 'COM_CCK_ADDON_'.CCK_NAME ); ?></div>
			<ul class="adminformlist">
				<li style="text-align:center;">
					<?php echo JText::_( 'COM_CCK_ADDON_'.CCK_NAME.'_DESC' ); ?>
				</li>
			</ul>
			<div class="clr"></div>
		</div>
	</div>
	<div class="seblod-less cpanel_news full">
		<?php echo JCckDevAccordion::start( 'cckOptions', 'collapse0', JText::_( 'COM_CCK_PANE_FROM_FILE' ), array( 'active'=>'collapse0',  'useCookie'=>'1' ) ); ?>
		<?php if ( $ajaxTotal ) { ?>
			<?php if ( $wait ) { ?>
			<div class="seblod cck-padding-top-0 cck-padding-bottom-0 cck-overflow-visible seblod-fix">
				<ul class="adminformlist">
					<li>
						<table class="table table-striped">
							<thead>
								<tr>
									<th width="30%"><?php echo JText::_( 'COM_CCK_CSV_COLUMN' )?></th>
									<th><?php echo JText::_( 'COM_CCK_FIELD_MAPPING' )?> <span class="icon-help hasTooltip" title="<?php echo JText::_( 'COM_CCK_AJAX_FIELD_MAPPING_DESC' ); ?>"></span></th>
									<th width="20%"><?php echo JText::_( 'COM_CCK_INPUT' )?> <span class="icon-help hasTooltip" title="<?php echo JText::_( 'COM_CCK_AJAX_PREPARE_INPUT_DESC' ); ?>"></span></th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ( isset( $session_data['csv']['columns'] ) ) {
									foreach ( $session_data['csv']['columns'] as $field_name ) {
										$js	.=	'$("#ajax_import_'.$field_name.'_input").isUpdatedWhen("ajax_import_'.$field_name.'_mapping");';

										echo '<tr>'
											.'<td><span class="column-name selected">'.$field_name.'</span></td>'
											.Helper_Import::getMappingCells( $field_name, $session_data, $config, $params->get( 'mode_ajax_mapping_field', 0 ) )
											.'</tr>'
											;
									}
								}
								?>
							</tbody>
						</table>
					</li>
				</ul>
			</div>
			<?php } ?>
			<div class="seblod cck-padding-top-0 cck-padding-bottom-0 seblod-fix">
				<div class="progress active" style="position:relative; top:5px;"><div id="progressbar" class="progress-bar progress-bar-striped" role="progressbar" style="width: 5%;"></div></div>
			</div>
			<?php if ( $wait ) { ?>
			<div class="seblod cck-padding-top-0 cck-overflow-visible seblod-fix">
				<div class="form-grid">
					<div class="control-group">
						<div class="controls text-center">
							<button type="button" class="btn" id="abort"><?php echo JText::_( 'COM_CCK_CANCEL' ); ?></button>
							<button type="button" class="button btn btn-primary" id="go"><?php echo JText::_( 'COM_CCK_IMPORT_NOW' ); ?></button>
						</div>
					</div>
				</div>
			</div>
			<?php }	?>
		<?php } else { ?>
			<div class="seblod">
				<div id="loading" class="loading"></div>
				<?php if ( $ajax ) { ?>
				<div class="form-grid">
					<div class="control-group">
						<div class="controls text-center">
							<?php echo JCckDev::getForm( 'more_importer_workflow', '', $config ); ?>
						</div>
					</div>
				</div>
				<?php } ?>
				<div class="form-grid dest-params">
					<div class="control-group required-params">
						<div class="control-label">
							<label><?php echo JText::_( 'COM_CCK_CONTENT_OBJECT' ); ?><span class="star"> *</span></label>
						</div>
						<div class="controls">
							<?php
							echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck_importer', 'function'=>'getObjectPlugins', 'name'=>'more_importer_storage_location' ), '', $config, array( 'storage_field'=>'options[storage_location]', 'css'=>'form-select' ) )
							.	 JCckDev::getForm( 'more_importer_storage', $this->params->get( 'storage', 'standard' ), $config, array( 'attributes'=>'hidden' ) );
							?>
						</div>
					</div>
					<div class="control-group required-params">
						<div class="control-label">
							<label><?php echo JText::_( 'COM_CCK_CONTENT_TYPE_FORM' ); ?><span class="star"> *</span></label>
						</div>
						<div class="controls">
						<?php echo JCckDev::getForm( 'more_importer_content_type', '', $config )
						 .	 JCckDev::getForm( 'more_importer_content_type_new', '', $config );
					 	?>
						</div>
					</div>
					<?php
					echo JCckDev::renderForm( 'more_importer_upload_file', '', $config, array(), array(), 'required-params' );
					echo JCckDev::renderForm( 'more_importer_force_utf8', $this->params->get( 'force_utf8', '1' ), $config );
					?>
					<div class="control-group">
						<div class="control-label">
							<label><?php echo JText::_( 'COM_CCK_INPUT' ); ?>
							</label>
						</div>
						<div class="controls">
					 		<?php echo JCckDev::getForm( 'more_importer_prepare_input', $this->params->get( 'prepare_input', '0' ), $config, array(), array( 'after'=>' <span class="icon-help hasTooltip" title="'.JText::_( 'COM_CCK_AJAX_PREPARE_INPUT_DESC' ).'"></span>' ) ); ?>
						</div>
					</div>
					<?php echo JCckDev::renderForm( 'more_importer_separator', $this->params->get( 'separator', ';' ), $config ); ?>
				</div>
			</div>
			<div id="layer" class="cck-padding-top-0 cck-padding-bottom-0">
				<?php /* Loaded by AJAX */ ?>
			</div>
			<div class="seblod cck-padding-top-0 cck-overflow-visible">
				<div class="form-grid">
					<?php
					if ( $ajax ) {
						$attr	=	'onclick="javascript:Joomla.submitbutton(\'importFromFileAjax\');"';
					} else {
						$attr	=	'onclick="javascript:Joomla.submitbutton(\'importFromFile\');"';
					}
					?>
					
					<div class="control-group">
						<div class="controls text-center">
							<div class="btn-group dropup">
								<?php echo JCckDev::getForm( 'more_importer_submit', '', $config, array( 'label'=>'Import from File', 'storage'=>'dev', 'attributes'=>$attr, 'css'=>'btn-primary' ) ); ?>
								<a href="javascript:void(0);" id="featured_session" class="btn btn-primary hasTooltip hasTip" title="Remember this session"><span class="icon-unarchive"></span></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php if ( !$ajaxTotal ) { ?>
		<?php echo JCckDevAccordion::open( 'cckOptions', 'collapse1', JText::_( 'COM_CCK_PANE_PREPARE_FILE' ) ); ?>
			<div class="seblod">
				<div id="loading" class="loading"></div>
				<div class="control-grid dest-params"></div>
			</div>
			<div class="seblod cck-padding-top-0 cck-overflow-visible">
				<div class="form-grid">
					<div class="control-group">
						<div class="controls text-center">
							<?php echo JCckDev::getForm( 'more_importer_submit', '', $config, array( 'label'=>'Prepare File', 'storage'=>'dev', 'attributes'=>'onclick="javascript:Joomla.submitbutton(\'prepareFile\');"', 'css'=>'btn-success' ) ); ?>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php echo JCckDevAccordion::end(); ?>
	</div>
</div>

<div class="clr"></div>
<div>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<?php
	JCckDev::validate( $config );
	echo JHtml::_( 'form.token' );
	?>
</div>

<?php
Helper_Display::quickCopyright();
Helper_Display::quickSession( array( 'extension'=>'com_cck_importer' ) );
?>
</div>
</form>

<script type="text/javascript">
(function ($){
	JCck.Dev = {
		ajaxStep:<?php echo $ajaxStep; ?>,
		ajaxTotal:<?php echo $ajaxTotal; ?>,
		token:Joomla.getOptions("csrf.token")+"=1",
		wait:<?php echo (int)$wait; ?>,
		ajaxLoopRequest: function(uri, start, end, len, total) {
			var map_data = '';
			if (start == 0) {
				map_data = '&map_data='+JCck.Dev.getMapData();
			}
			$.ajax({
				data: JCck.Dev.token+map_data,
				type: "POST",
				url:  uri+"&start="+start+"&end="+end,
				beforeSend:function(){},
				success: function(resp) {
					var percent = end / total * 100;
					percent = percent >= 5 ? percent : 5;
					start   = start+len;
					end     = end+len;
					jQuery("#progressbar").css("width",percent+"%");
					if (start < total) {
						JCck.Dev.ajaxLoopRequest(uri, start, end, len, total);
					} else {
						var data = (resp != "") ? $.evalJSON(resp) : "";
						var link = (data.file) ? "" : data.link;
						if (!link) {
							link = "index.php?option=com_cck_importer";
						}
						document.location.href=data.link;
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {}
			});
		},
		ajaxLayer: function(view, layout, elem, mydata, myopts) {
			var loading = "<img align='center' src='<?php echo $ajax_load; ?>' alt='' />";  
			$.ajax({
				cache: false,
				data: mydata,
				type: "POST",
				url: "index.php?option=com_cck_importer&view="+view+"&layout="+layout+"&format=raw",
				beforeSend:function(){ $("#loading").html(loading); $(elem).html(""); },
				success: function(response){ $("#loading").html(""); $(elem).css("opacity", 0.4).html(response).fadeTo("fast",1); if (myopts) { JCck.Dev.setSession(myopts); } },
				error:function(){ $(elem).html("<div><strong>Oops!</strong> Try to close the page & re-open it properly.</div>"); }
			});
		},
		ajaxSession: function(opts, key, val) {
			if ( key != "" ) {
				var cur = $("#"+key).myVal();
			}
			if ( key != "" && cur != val ) {
				var data = "&ajax_type="+val;
				JCck.Dev.ajaxLayer("cck_importer", "default2", "#layer", data, opts);
			} else {
				JCck.Dev.setSession(opts);
			}
		},
		ajaxSessionDelete: function(sid) {
			var loading = "<img align='center' src='<?php echo $ajax_load; ?>' alt='' />"; 
			$.ajax({
				cache: false,
				data: "sid="+sid+"&"+JCck.Dev.token,
				type: "POST",
				url: "index.php?option=com_cck&task=deleteSessionAjax&format=raw",
				beforeSend:function(){ $("#loading").html(loading); },
				success: function(){ $("#loading").html(""); document.location.reload(); }
			});
		},
		ajaxSessionSave: function() {
			var loading = "<img align='center' src='<?php echo $ajax_load; ?>' alt='' />"; 
			var data = {};
			var id = '';
			$("#adminForm input.text, #adminForm select.select, #adminForm select.tag, #adminForm fieldset.checkboxes, #adminForm fieldset.radios").each(function(i) {
				id = $(this).attr("id");
				data[id] = String($(this).myVal());
			});
			var encoded	= $.toJSON(data);
			var type = $("#options_storage_location").val();
			$.ajax({
				cache: false,
				data: "data="+encoded+"&extension=com_cck_importer&folder=1&type="+type+"&"+JCck.Dev.token,
				type: "POST",
				url: "index.php?option=com_cck&task=saveSessionAjax&format=raw",
				beforeSend:function(){ $("#loading").html(loading); },
				success: function(){ $("#loading").html(""); document.location.reload(); }
			});
		},
		getMapData: function(){
			var map_data = {};
			
			$(".map-data").each(function(i) {
				var n = $(this).attr("data-name");
				
				map_data[n] = {};
				map_data[n].map = $(this).val();

				if (map_data[n].map != 'clear') {
					if ($('#ajax_import_'+n+'_input').prop('disabled') !== true) {
						map_data[n].input = $('#ajax_import_'+n+'_input').val();
					} else {
						map_data[n].input = '0';
					}
				}
			});

			return $.toJSON(map_data);
		},
		setSession: function(opts) {
			var data = $.evalJSON(opts);
			$.each(data, function(k, v) {
				$("#"+k).myVal(v);

				switch( k ) {
					case "options_force_utf8":
	                	if ( v == "1" ) {
	                		$("label[for='options_force_utf80']").addClass("active btn-success");
	                		$("label[for='options_force_utf81']").removeClass("active btn-danger");
	                	} else {
	                		$("label[for='options_force_utf80']").removeClass("active btn-success");
	                		$("label[for='options_force_utf81']").addClass("active btn-danger");
	                	}
	                	break;
                }
			});
			if (typeof JCck.Dev.applyConditionalStates === 'function') {
                JCck.Dev.applyConditionalStates();
            }
		},
		submit: function(task) {
			Joomla.submitbutton(task);
		},
		toggleOptions: function(cur,clear) {
			$('#options_content_type option').show();
			if (cur) {
				var v = "";
				$('#options_content_type option').each(function() {
					v = $(this).attr("data-object");
					if (v != "" && v !== undefined && v != cur) {
						$(this).hide();
					}
				});
			}
			if (clear && $('#options_content_type').val() != "-1") {
                $('#options_content_type').val("");
            }
		},
		trigger: function() {
			JCck.Dev.ajaxLoopRequest('index.php?option=com_cck_importer&task=importFromFileAjax&format=raw', 0, JCck.Dev.ajaxStep, JCck.Dev.ajaxStep, JCck.Dev.ajaxTotal);
		}
	};
	Joomla.submitbutton = function(task) {
		if (task == "purge") {
			if (confirm(Joomla.JText._('COM_CCK_CONFIRM_PURGE_OUTPUT_FOLDER'))) {
				Joomla.submitform(task);
			} else {
				return false;
			}
		} else if ($("#adminForm").validationEngine("validate",task) === true) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	};
	$.fn.isUpdatedWhen = function(id) {
		var $el = $(this);
		var $trigger = $('#'+id);

		if ($trigger != null) {
			$trigger.change( function() {
				var selected = $trigger.val();

				if (selected == "clear") {
					$el.prop("disabled",true).hide();
					$el.parent().find('.info-raw').hide();
				} else if (!selected) {
					if ($trigger.attr('data-field') == "0") {
						$el.prop("disabled",true).hide();
						$el.parent().find('.info-raw').show();
					} else {
						$el.prop("disabled",false).show();
						$el.parent().find('.info-raw').hide();
					}
				} else {
					if ($trigger.find("option:selected").attr("data-field-option") == "1") {
						$el.prop("disabled",false).show();
						$el.parent().find('.info-raw').hide();
					} else {
						$el.prop("disabled",true).hide();
						$el.parent().find('.info-raw').show();
					}
				}
			});
		}
	};
	$(document).ready(function() {
		JCck.Dev.toggleOptions($("#options_storage_location").val(),false);

		$("#options_storage_location").on('change', function() {
			var data = "&ajax_type="+$(this).val();
			JCck.Dev.toggleOptions($(this).val(),true);
			JCck.Dev.ajaxLayer("cck_importer", "default2", "#layer", data);
		});
		$("#featured_session").on("click", function() {
			JCck.Dev.ajaxSessionSave();
		});
		$(".featured_sessions").on("click", function() {
			JCck.Dev.ajaxSession($(this).attr("mydata2"), "options_storage_location", $(this).attr("mydata"));
		});
		$(".featured_sessions_del").on("click", function(e) {
			e.preventDefault();
			JCck.Dev.ajaxSessionDelete($(this).attr("mydata"));
		});
		$('#options_content_type_new').isVisibleWhen('options_content_type','-1',false);

		$('.accordion-button').on('click', function () {
			$('.required-params').prependTo($(this).attr('data-bs-target')+' .dest-params');
		})

		/* Ajax::start */
		if (JCck.Dev.ajaxTotal > 0) {
			if (JCck.Dev.wait) {
				$("#abort").on("click", function() {
					document.location.href="index.php?option=com_cck_importer";
				});
				$("#go").on("click", function() {
					if ($("#adminForm").validationEngine("validate","go") === true) {
						JCck.Dev.trigger();
					}
				});
			} else {
				JCck.Dev.trigger();
			}
		} else {
			JCck.Dev.ajaxLayer("cck_importer", "default2", "#layer", "&ajax_type="+$("#options_storage_location").val());
		}
		/* Ajax::end */

		<?php echo $js; ?>
	});
})(jQuery);
</script>