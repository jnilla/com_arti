<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Jnilla\Jom\Jom as Jom;
use Jnilla\Lara\Helper\Base as BaseHelper;

// Framework re-usable template
require JPATH_LIBRARIES."/lara/assets/layouts/admin_item_default_init.php";

Jom::css('media/com_arti/css/package.css');
Jom::js('media/com_arti/js/package.js');

$form = $this->form;
$fieldsets = $form->getFieldsets();
$id = $this->item->id;
$nameVariations = ArtiHelper::generateNameVariations('packageName', $this->item->name);
extract($nameVariations);
$dbPrefix = Jom::conf('dbprefix');
?>
<form name="adminForm" action="" method="post" 
	enctype="multipart/form-data" id="<?php echo $singularNameInLowerCase; ?>-form" 
	class="form-validate form-horizontal">
	
    <?php
    foreach ($fieldsets as $fieldset){
        $displaydata['items'][] = [
            'title' => Jom::translate($fieldset->label),
            'content' => Jom::rederFieldset($form, $fieldset->name, true),
        ];
    }
    ?>

    <?php ob_start(); ?>
    <div class="alert hidden" id="save-changes-alert"> <?php echo Jom::translate('COM_ARTI_MESSAGE_SAVE_CHANGES_FIRST'); ?></div>

    <div class="accordion" id="commands-accordion">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#commands-accordion" href="#commands-accordion-accordion-1">
                    <?php echo Jom::translate('COM_ARTI_HEADING_INITIALIZE_PACKAGE'); ?>
                </a>
            </div>
            <div id="commands-accordion-accordion-1" class="accordion-body collapse">
                <div class="accordion-inner">
                    <div class="control-group">
                        <div onclick="Joomla.submitbutton('package.initializepackage');" class="btn btn-info">
                            <i class="icon-play-circle"></i> <?php echo Jom::translate('COM_ARTI_BUTTON_INITIALIZE'); ?>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#commands-accordion" href="#commands-accordion-accordion-2">
                    <?php echo Jom::translate('COM_ARTI_HEADING_BUILD_PACKAGE'); ?>
                </a>
            </div>
            <div id="commands-accordion-accordion-2" class="accordion-body collapse">
                <div class="accordion-inner">
                    <div class="control-group">
                        <div onclick="Joomla.submitbutton('package.buildpackage');" class="btn btn-primary">
                            <i class="icon-play-circle"></i> <?php echo Jom::translate('COM_ARTI_BUTTON_BUILD'); ?>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#commands-accordion" href="#commands-accordion-accordion-3">
                    <?php echo Jom::translate('COM_ARTI_HEADING_CREATE_CRUD_VIEWS'); ?>
                </a>
            </div>
            <div id="commands-accordion-accordion-3" class="accordion-body collapse">
                <div class="accordion-inner">
                    <?php echo Jom::renderFieldOnce($form, 'new_crud_view_name'); ?>
                    <?php echo Jom::renderFieldOnce($form, 'new_crud_view_side'); ?>
                    <div class="control-group">
                        <div onclick="Joomla.submitbutton('package.createcrudviews');" class="btn btn-success">
                            <i class="icon-play-circle"></i> <?php echo Jom::translate('COM_ARTI_BUTTON_CREATE'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#commands-accordion" href="#commands-accordion-accordion-4">
                    <?php echo Jom::translate('COM_ARTI_HEADING_CREATE_TABLE'); ?>
                </a>
            </div>
            <div id="commands-accordion-accordion-4" class="accordion-body collapse">
                <div class="accordion-inner">
                    <?php echo Jom::renderFieldOnce($form, 'new_table_name'); ?>
                    <div class="control-group">
                        <div onclick="Joomla.submitbutton('package.createtable');" class="btn btn-success">
                            <i class="icon-play-circle"></i> <?php echo Jom::translate('COM_ARTI_BUTTON_CREATE'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#commands-accordion" href="#commands-accordion-accordion-5">
                    <?php echo Jom::translate('COM_ARTI_HEADING_CREATE_FIELD'); ?>
                </a>
            </div>
            <div id="commands-accordion-accordion-5" class="accordion-body collapse">
                <div class="accordion-inner">
                    <?php
                    $tables = Jom::db()->getTableList();
                    foreach ($tables as $table) {
                        if(preg_match('/^'.$dbPrefix.$packageNameInLowerCaseNoSpace.'_/', $table)){
                            Jom::addFieldOption($form, 'new_form_field_table', $table, $table);
                        }
                    }
                    echo Jom::renderFieldOnce($form, 'new_form_field_table');
                    echo Jom::renderFieldOnce($form, 'new_form_field_type');
                    echo Jom::renderFieldOnce($form, 'new_form_field_name');
                    ?>
                    <div class="control-group">
                        <div onclick="Joomla.submitbutton('package.createfield');" class="btn btn-success">
                            <i class="icon-play-circle"></i> <?php echo Jom::translate('COM_ARTI_BUTTON_CREATE'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#commands-accordion" href="#commands-accordion-accordion-6">
                    <?php echo Jom::translate('COM_ARTI_HEADING_CREATE_HTML_VIEW'); ?>
                </a>
            </div>
            <div id="commands-accordion-accordion-6" class="accordion-body collapse">
                <div class="accordion-inner">
                    <?php echo Jom::renderFieldOnce($form, 'new_html_view_name'); ?>
                    <div class="control-group">
                        <a href="<?php echo "index.php?option=com_arti&view=package&task=package.createhtmlview&id=$id"; ?>" class="btn btn-success"><i class="icon-play-circle"></i> <?php echo Jom::translate('COM_ARTI_BUTTON_CREATE'); ?></a>
                    </div>
                </div>
            </div>
        </div> -->

    </div>
    <?php $buffer = ob_get_clean(); ?>

    <?php 
    $displaydata['items'][] = [
        'title' => Jom::translate('COM_ARTI_FIELDSET_COMMANDS'),
        'content' => $buffer,
    ];
    $displaydata['id'] = "commands";

    echo Jom::layout(
        'stateful_tabs', 
        $displaydata, 
        JPATH_LIBRARIES."/lara/assets/layouts"
    );
    ; ?>

	<!-- Hidden fields -->
	<input type="hidden" name="option" value="<?php echo "com_$componentNameInLowerCase"; ?>"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="layout" value="edit"/>
	<?php echo Jom::formToken(); ?>
	<?php Jom::renderHiddenFields($form); ?>
	<!-- Hidden fields - End -->
</form>


