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
JFactory::getDocument()->addStyleDeclaration( 'div.seblod .adminformlist button {margin:0;} div.seblod .adminformlist-2cols li {margin:0;} #system-message-container.j-toggle-main.span10{width: 100%;}' );

$ajaxStep	=	0;
$ajaxTotal	=	0;
$do			=	$app->input->get( 'do', '' );
if ( $do == 'ok' ) {
	$res	=	$session->get( 'cck_importer_batch_ok' );
	if ( isset( $res['message'] ) && $res['message'] ) {
		$app->enqueueMessage( $res['message'], $res['message_type'] );
	}
} elseif ( $do && $session->get( 'cck_importer_batch' ) ) {
	$ajaxStep	=	$params->get( 'mode_ajax_count', 25 );
	$ajaxTotal	=	(int)$do;
} else {
	$session->set( 'cck_importer_batch', '' );
}
$session->set( 'cck_importer_batch_ok', '' );
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
        <?php echo JCckDevAccordion::start( 'cckOptions', 'collapse0', JText::_( 'COM_CCK_PANE_FROM_FILE' ), array( 'active'=>'collapse0', 'useCookie'=>'1' ) ); ?>
        <?php if ( $ajaxTotal ) { ?>
			<div class="progress progress-striped active" style="position:relative; top:5px;"><div id="progressbar" class="bar" style="width: 5%;"></div></div>	
        <?php } else { ?>
	        <div class="seblod">
				<div id="loading" class="loading"></div>
	            <ul class="adminformlist">
					<?php
					echo '<li><label>'.JText::_( 'COM_CCK_CONTENT_OBJECT' ).'<span class="star"> *</span></label>'
					 .	 JCckDev::getFormFromHelper( array( 'component'=>'com_cck_importer', 'function'=>'getObjectPlugins', 'name'=>'more_importer_storage_location' ), '', $config, array( 'storage_field'=>'options[storage_location]' ) )
					 .	 JCckDev::getForm( 'more_importer_storage', $this->params->get( 'storage', 'standard' ), $config )
					 .	 '</li>';
					echo '<li><label>'.JText::_( 'COM_CCK_CONTENT_TYPE_FORM' ).'<span class="star"> *</span></label>'
					 .	 JCckDev::getForm( 'more_importer_content_type', '', $config )
					 .	 JCckDev::getForm( 'more_importer_content_type_new', '', $config )
					 .	 '</li>';
	                echo JCckDev::renderForm( 'more_importer_upload_file', '', $config );
	                echo JCckDev::renderForm( 'more_importer_separator', $this->params->get( 'separator', ';' ), $config );
	                echo JCckDev::renderForm( 'more_importer_force_utf8', $this->params->get( 'force_utf8', '1' ), $config );
	                ?>
	            </ul>
	        </div>
	        <div id="layer" class="cck-padding-top-0 cck-padding-bottom-0">
	            <?php /* Loaded by AJAX */ ?>
	        </div>
	        <div class="seblod cck-padding-top-0 cck-overflow-visible">
	            <ul class="adminformlist">
					<?php
					if ( $ajax ) {
						$attr	=	'onclick="javascript:Joomla.submitbutton(\'importFromFileAjax\');"';
					} else {
						$attr	=	'onclick="javascript:Joomla.submitbutton(\'importFromFile\');"';
					}
					echo '<li class="btn-group dropup flt-right">'
					 .	 JCckDev::getForm( 'more_importer_submit', '', $config, array( 'label'=>'Import from File', 'storage'=>'dev', 'attributes'=>$attr, 'css'=>'btn-primary' ) );
					echo '<a href="javascript:void(0);" id="featured_session" class="btn btn-primary hasTooltip hasTip" title="Remember this session"><span class="icon-unarchive"></span></a>';
					echo '</li><li>&nbsp;</li>';
	                ?>
	            </ul>
	        </div>
        <?php } ?>
        </div>
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
		ajaxLoopRequest: function(uri, start, end, len, total) {
			$.ajax({
				data: JCck.Dev.token,
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
			$("#adminForm input.text, #adminForm select.select, #adminForm fieldset.checkbox, #adminForm fieldset.radios").each(function(i) {
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
		}
	}
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
	}
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
		/* Ajax::start */
		if (JCck.Dev.ajaxTotal > 0) {
			var uri = "index.php?option=com_cck_importer&task=importFromFileAjax&format=raw";
			JCck.Dev.ajaxLoopRequest(uri, 0, JCck.Dev.ajaxStep, JCck.Dev.ajaxStep, JCck.Dev.ajaxTotal);
		} else {
			JCck.Dev.ajaxLayer("cck_importer", "default2", "#layer", "&ajax_type="+$("#options_storage_location").val());
		}
		/* Ajax::end */
	});
})(jQuery);
</script>