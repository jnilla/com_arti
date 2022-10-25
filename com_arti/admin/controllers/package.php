<?php
defined('_JEXEC') or die();

use Jnilla\Lara\Controller\AdminItem as AdminItemController;
use Jnilla\Jom\Jom as Jom;

/**
 * Package controller class
 */
class ArtiControllerPackage extends AdminItemController{
	/**
     * Initialize a package
     *
     * @return void
     */
    public function initializePackage()
    {
		// Check if the package form was saved first
		$id = Jom::req('id', 0, 'INT');
		if($id === 0){
			Jom::message(Jom::translate('COM_ARTI_ERROR_SAVE_PACKAGE_FIRST'), 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		$package = ArtiHelper::getPackage($id);

		// DEBUG >>>
		Jom::message('DEBUG: package deleted:'.$package['package_path'], 'warning');
		JFolder::delete($package['package_path']);
		// <<< DEBUG

		// Abort if package folder already exist
		if(file_exists($package['package_path'])){
			Jom::message(Jom::translate('COM_ARTI_ERROR_PACKAGE_ALREADY_EXIST').$package['package_path'], 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		// Initialize the package by type
		switch ($package['type']) {
			case 'joomla_component':
				ArtiHelper::initializeJoomlaComponentPackage($package);
				break;
		}
		
		// Success
		Jom::message(Jom::translate('COM_ARTI_MESSAGE_PACKAGE_INITIALIZED'));
		Jom::redirect("index.php?option=com_arti&view=package&id=$id");
    }

	/**
     * Build a package
     *
     * @return void
     */
    public function buildPackage()
    {
		// Check if the package form was saved first
		$id = Jom::req('id', 0, 'INT');
		if($id === 0){
			Jom::message(Jom::translate('COM_ARTI_ERROR_SAVE_PACKAGE_FIRST'), 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		$package = ArtiHelper::getPackage($id);
		$packageAdminPath = JPATH_ADMINISTRATOR."/components/".$package['package_filename'];

		// Abort if package is not installed 
		if(!file_exists($packageAdminPath)){
			Jom::message(Jom::translate('COM_ARTI_ERROR_PACKAGE_MUST_BE_INSTALLED').$package['package_filename'], 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		// Build the package by type
		switch ($package['type']) {
			case 'joomla_component':
				ArtiHelper::buildJoomlaComponentPackage($package);
				break;
		}

		// Success
		Jom::message(Jom::translate('COM_ARTI_MESSAGE_PACKAGE_BUILD_SUCCESSFUL'));
		Jom::redirect("index.php?option=com_arti&view=package&id=$id");
    }

	/**
	 * Executes after the form is saved.
	 *
	 * @param   \JModelLegacy  $model      The data model object.
	 * @param   array          $validData  The validated data.
	 *
	 * @return  void
	 */
	protected function postSaveHook(\JModelLegacy $model, $validData = array())
	{
		// Define package name variations
		$packageNameVariations = ArtiHelper::generateNameVariations('packageName', $validData['name']);
		extract($packageNameVariations);

		// Define package filename
		switch ($validData['type']) {
			case 'joomla_component':
				$packageFilename = 'com_';
				break;
			case 'joomla_frontend_module':
				$packageFilename = 'mod_';
				break;
			case 'joomla_system_pluglin':
				$packageFilename = 'plg_system_';
				break;
			case 'joomla_content_pluglin':
				$packageFilename = 'plg_content_';
				break;
			case 'joomla_library':
				$packageFilename = 'lib_';
				break;
			case 'composer_library':
				$packageFilename = '';
				break;
		}
		$packageFilename .= $packageNameInLowerCaseNoSpace;

		// Define package path
		$packagePath = rtrim($validData['package_base_path'], '/');
		$packagePath = $packagePath.'/'.$packageFilename;

		// Define package update server manifest path
		$updateServerManifestUrl = rtrim($validData['update_server_base_url'], '/');
		$updateServerManifestUrl = $updateServerManifestUrl.'/'.$packageFilename.'/updates.xml';

		// Store new data
		Jom::query(
			"UPDATE #__arti_packages
			SET 
				package_filename = ':packageFilename',
				package_path = ':packagePath',
				update_server_manifest_url = ':updateServerManifestUrl'
			WHERE
				id = ':id'
			",
			[
				'packageFilename' => $packageFilename,
				'packagePath' => $packagePath,
				'updateServerManifestUrl' => $updateServerManifestUrl,
				'id' => $validData['id'],
			]
		);
	}

	/**
     * Creates a new database table
     *
     * @return void
     */
    public function createTable()
    {
		// Check if the package form was saved first
		$id = Jom::req('id', 0, 'INT');
		if($id === 0){
			Jom::message(Jom::translate('COM_ARTI_ERROR_SAVE_PACKAGE_FIRST'), 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		$package = ArtiHelper::getPackage($id);
		$tables = Jom::db()->getTableList();
		$dbPrefix = Jom::conf('dbprefix');
		$nameVariations = ArtiHelper::generateNameVariations('packageName', $package['name']);
		extract($nameVariations);
		$newTableName = Jom::req('jform[new_table_name]');
		$newTableFullName = $dbPrefix.$packageNameInLowerCaseNoSpace.'_'.$newTableName;

		// Abort if table already exist
		if(in_array($newTableFullName, $tables)){
			Jom::message(Jom::translate('COM_ARTI_ERROR_TABLE_ALREADY_EXIST').$newTableFullName, 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		// Create new table
		Jom::query(
			"CREATE TABLE `:NewTableFullName` (
				`id` int unsigned NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
			[
				'NewTableFullName' => '#__'.$packageNameInLowerCaseNoSpace.'_'.$newTableName
			]
		);
		
		// Success
		Jom::message(Jom::translate('COM_ARTI_MESSAGE_TABLE_CREATED'));
		Jom::redirect("index.php?option=com_arti&view=package&id=$id");
    }

	/**
     * Creates a new database table field
     *
     * @return void
     */
    public function createField()
    {
		// Check if the package form was saved first
		$id = Jom::req('id', 0, 'INT');
		if($id === 0){
			Jom::message(Jom::translate('COM_ARTI_ERROR_SAVE_PACKAGE_FIRST'), 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		$tableName = Jom::req('jform[new_form_field_table]');

		// Check if table name is valid
		if($tableName === ''){
			Jom::message(Jom::translate('COM_ARTI_MESSAGE_INVALID_TABLE_NAME'), 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		$fieldType= Jom::req('jform[new_form_field_type]');

		$fieldName = Jom::req('jform[new_form_field_name]');

		// Check if field name is valid
		if($fieldName === ''){
			Jom::message(Jom::translate('COM_ARTI_MESSAGE_INVALID_TABLE_FIELD_NAME'), 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		$fields = Jom::db()->getTableColumns($tableName);

		// Abort if field already exist
		if(array_key_exists($fieldName, $fields)){
			Jom::message(Jom::translate('COM_ARTI_ERROR_TABLE_FIELD_ALREADY_EXIST').$fieldName, 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		// Create query by field type
		switch ($fieldType) {
			case 'varchar_255':
				$query = "VARCHAR(255) NOT NULL";
				break;
			case 'datetime':
				$query = "DATETIME NOT NULL";
				break;
			case 'text':
				$query = "TEXT NOT NULL";
				break;
			case 'mediumtext':
				$query = "MEDIUMTEXT NOT NULL";
				break;
			case 'longtext':
				$query = "LONGTEXT NOT NULL";
				break;
			case 'int':
				$query = "INT NOT NULL";
				break;
			case 'float':
				$query = "INT NOT NULL";
				break;
			case 'boolean':
				$query = "BOOLEAN NOT NULL";
				break;
		}
		$query = "ALTER TABLE `:tableName` ADD `:fieldName` $query";

		// Create new field
		Jom::query(
			$query,
			[
				'tableName' => $tableName,
				'fieldName' => $fieldName,
			]
		);
		
		// Success
		Jom::message(Jom::translate('COM_ARTI_MESSAGE_TABLE_FIELD_CREATED'));
		Jom::redirect("index.php?option=com_arti&view=package&id=$id");
    }

	/**
     * Create CRUD views
     *
     * @return void
     */
    public function createCrudViews()
    {
		// Check if the package form was saved first
		$id = Jom::req('id', 0, 'INT');
		if($id === 0){
			Jom::message(Jom::translate('COM_ARTI_ERROR_SAVE_PACKAGE_FIRST'), 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		$package = ArtiHelper::getPackage($id);
		$viewName = Jom::req('jform[new_crud_view_name]');
		$viewNameVariations = ArtiHelper::generateNameVariations('viewName', $viewName);
		extract($viewNameVariations);
		$viewNamePlural = $viewName.'s';
		$viewNamePluralVariations = ArtiHelper::generateNameVariations('viewNamePlural', $viewNamePlural);
		extract($viewNamePluralVariations);
		$side = Jom::req('jform[new_crud_view_side]');
		$packageNameVariations = ArtiHelper::generateNameVariations('packageName', $package['name']);
		extract($packageNameVariations);
		$packageTemplatePath = JPATH_COMPONENT_ADMINISTRATOR."/assets/package_templates/joomla_component/com_artiexample";
		
		$packageInstallationAdminPath = JPATH_SITE."/administrator/components/".$package['package_filename'];
		$packageInstallationSitePath = JPATH_SITE."/components/".$package['package_filename'];

		// Abort if package is not installed
		if(!file_exists($packageInstallationAdminPath)){
			Jom::message(Jom::translate('COM_ARTI_ERROR_PACKAGE_MUST_BE_INSTALLED').$package['package_filename'], 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		$tables = Jom::db()->getTableList();
		$dbPrefix = Jom::conf('dbprefix');
		$newTableFullName = $dbPrefix.$packageNameInLowerCaseNoSpace.'_'.$viewNamePluralInLowerCaseNoSpace;

		// Abort if table already exist
		if(in_array($newTableFullName, $tables)){
			Jom::message(Jom::translate('COM_ARTI_ERROR_TABLE_ALREADY_EXIST').$newTableFullName, 'error');
			Jom::redirect("index.php?option=com_arti&view=package&id=$id");
		}

		// Add table
		Jom::query(
			"CREATE TABLE `:NewTableFullName` (
				`id` int unsigned NOT NULL AUTO_INCREMENT,
				`title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
				`status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
				`note` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
			[
				'NewTableFullName' => '#__'.$packageNameInLowerCaseNoSpace.'_'.$viewNamePluralInLowerCaseNoSpace
			]
		);

		// Copy model files
		if(($side === 'admin') || ($side === 'both')){
			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/admin/models/examplenote.php",
				"$packageInstallationAdminPath/models/$viewNameInLowerCaseNoSpace.php"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/models/$viewNameInLowerCaseNoSpace.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNote', $viewNameInPascalCase],
				]
			);

			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/admin/models/examplenotes.php",
				"$packageInstallationAdminPath/models/$viewNamePluralInLowerCaseNoSpace.php"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/models/$viewNamePluralInLowerCaseNoSpace.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNotes', $viewNamePluralInPascalCase],
				]
			);
		}
		if(($side === 'site') || ($side === 'both')){
			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/site/models/examplenote.php",
				"$packageInstallationSitePath/models/$viewNameInLowerCaseNoSpace.php"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationSitePath/models/$viewNameInLowerCaseNoSpace.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNote', $viewNameInPascalCase],
				]
			);

			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/site/models/examplenotes.php",
				"$packageInstallationSitePath/models/$viewNamePluralInLowerCaseNoSpace.php"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationSitePath/models/$viewNamePluralInLowerCaseNoSpace.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNotes', $viewNamePluralInPascalCase],
				]
			);
		}

		// Copy form xml files
		if(($side === 'admin') || ($side === 'both')){
			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/admin/models/forms/examplenote.xml",
				"$packageInstallationAdminPath/models/forms/$viewNameInLowerCaseNoSpace.xml"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/models/forms/$viewNameInLowerCaseNoSpace.xml",
				[
					['ARTIEXAMPLE', $packageNameInUpperCaseNoSpace],
				]
			);

			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/admin/models/forms/filter_examplenotes.xml",
				"$packageInstallationAdminPath/models/forms/filter_$viewNamePluralInLowerCaseNoSpace.xml"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/models/forms/filter_$viewNamePluralInLowerCaseNoSpace.xml",
				[
					['ARTIEXAMPLE', $packageNameInUpperCaseNoSpace],
				]
			);
		}
		if(($side === 'site') || ($side === 'both')){
			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/site/models/forms/examplenote.xml",
				"$packageInstallationSitePath/models/forms/$viewNameInLowerCaseNoSpace.xml"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationSitePath/models/forms/$viewNameInLowerCaseNoSpace.xml",
				[
					['ARTIEXAMPLE', $packageNameInUpperCaseNoSpace],
				]
			);

			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/site/models/forms/filter_examplenotes.xml",
				"$packageInstallationSitePath/models/forms/filter_$viewNamePluralInLowerCaseNoSpace.xml"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationSitePath/models/forms/filter_$viewNamePluralInLowerCaseNoSpace.xml",
				[
					['ARTIEXAMPLE', $packageNameInUpperCaseNoSpace],
				]
			);
		}
		
		// Copy view files
		if(($side === 'admin') || ($side === 'both')){
			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/admin/views/examplenote",
				"$packageInstallationAdminPath/views/$viewNameInLowerCaseNoSpace"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/views/$viewNameInLowerCaseNoSpace/view.html.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNote', $viewNameInPascalCase],
				]
			);

			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/admin/views/examplenotes",
				"$packageInstallationAdminPath/views/$viewNamePluralInLowerCaseNoSpace"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/views/$viewNamePluralInLowerCaseNoSpace/view.html.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNote', $viewNameInPascalCase],
				]
			);
		}
		if(($side === 'site') || ($side === 'both')){
			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/site/views/examplenote",
				"$packageInstallationSitePath/views/$viewNameInLowerCaseNoSpace"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationSitePath/views/$viewNameInLowerCaseNoSpace/view.html.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNote', $viewNameInPascalCase],
				]
			);

			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/site/views/examplenotes",
				"$packageInstallationSitePath/views/$viewNamePluralInLowerCaseNoSpace"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationSitePath/views/$viewNamePluralInLowerCaseNoSpace/view.html.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNote', $viewNameInPascalCase],
				]
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationSitePath/views/$viewNamePluralInLowerCaseNoSpace/tmpl/default.xml",
				[
					['ARTIEXAMPLE', $packageNameInUpperCaseNoSpace],
					['EXAMPLENOTE', $viewNameInUpperCaseNoSpace],
				]
			);
		}

		// Copy controller files
		if(($side === 'admin') || ($side === 'both')){
			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/admin/controllers/examplenote.php",
				"$packageInstallationAdminPath/controllers/$viewNameInLowerCaseNoSpace.php"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/controllers/$viewNameInLowerCaseNoSpace.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNote', $viewNameInPascalCase],
				]
			);

			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/admin/controllers/examplenotes.php",
				"$packageInstallationAdminPath/controllers/$viewNamePluralInLowerCaseNoSpace.php"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/controllers/$viewNamePluralInLowerCaseNoSpace.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNotes', $viewNamePluralInPascalCase],
				]
			);
		}
		if(($side === 'site') || ($side === 'both')){
			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/site/controllers/examplenote.php",
				"$packageInstallationSitePath/controllers/$viewNameInLowerCaseNoSpace.php"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationSitePath/controllers/$viewNameInLowerCaseNoSpace.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNote', $viewNameInPascalCase],
				]
			);

			Jom::copy(
				"$packageTemplatePath/repo/com_artiexample/site/controllers/examplenotes.php",
				"$packageInstallationSitePath/controllers/$viewNamePluralInLowerCaseNoSpace.php"
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationSitePath/controllers/$viewNamePluralInLowerCaseNoSpace.php",
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNotes', $viewNamePluralInPascalCase],
				]
			);
		}

		// Copy table files
		Jom::copy(
			"$packageTemplatePath/repo/com_artiexample/admin/tables/examplenotes.php",
			"$packageInstallationAdminPath/tables/$viewNamePluralInLowerCaseNoSpace.php"
		);
		ArtiHelper::replaceFileContent(
			"$packageInstallationAdminPath/tables/$viewNamePluralInLowerCaseNoSpace.php",
			[
				['ArtiExample', $packageNameInPascalCase],
				['ExampleNotes', $viewNamePluralInPascalCase],
			]
		);

		// Add configs to framework-configurations.php file
		if(($side === 'admin') || ($side === 'both')){
			// Add sidebar item
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/framework-configurations.php",
				[
					['/*DO_NOT_DELETE_THIS_COMMENT___SIDEBAR_ITEMS*/', "/*DO_NOT_DELETE_THIS_COMMENT___SIDEBAR_ITEMS*/\n\t\t'$viewNamePluralInLowerCaseNoSpace',"],
				]
			);
			
			// Add CRUD views configs
			$content = file_get_contents("$packageTemplatePath/repo/com_artiexample/admin/framework-configurations.php");
			$content = ArtiHelper::getContentBlock(
				$content, 
				'/*DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS*/',
				'/*DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS_END*/'
			);
			$content = ArtiHelper::replaceContent(
				$content,
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNote', $viewNameInPascalCase],
				]
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/framework-configurations.php",
				[
					['/*DO_NOT_DELETE_THIS_COMMENT___NEW_ITEMS*/', "/*DO_NOT_DELETE_THIS_COMMENT___NEW_ITEMS*/$content"],
				]
			);
		}
		if(($side === 'site') || ($side === 'both')){
			// Add CRUD views configs
			$content = file_get_contents("$packageTemplatePath/repo/com_artiexample/site/framework-configurations.php");
			$content = ArtiHelper::getContentBlock(
				$content, 
				'/*DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS*/',
				'/*DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS_END*/'
			);
			$content = ArtiHelper::replaceContent(
				$content,
				[
					['ArtiExample', $packageNameInPascalCase],
					['ExampleNote', $viewNameInPascalCase],
				]
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationSitePath/framework-configurations.php",
				[
					['/*DO_NOT_DELETE_THIS_COMMENT___NEW_ITEMS*/', "/*DO_NOT_DELETE_THIS_COMMENT___NEW_ITEMS*/$content"],
				]
			);
		}
		
		// Add language strings
		if(($side === 'admin') || ($side === 'both')){
			// Add CRUD views strings
			$content = file_get_contents("$packageTemplatePath/repo/com_artiexample/admin/language/en-GB/en-GB.com_artiexample.ini");
			$content = ArtiHelper::getContentBlock(
				$content, 
				'#DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS',
				'#DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS_END'
			);
			$content = ArtiHelper::replaceContent(
				$content,
				[
					['ARTIEXAMPLE', $packageNameInUpperCaseNoSpace],
					['EXAMPLENOTE', $viewNameInUpperCaseNoSpace],
					['Example Note', $viewNameInCapitalized],
				]
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/language/en-GB/en-GB.com_$packageNameInLowerCaseNoSpace.ini",
				[
					['#DO_NOT_DELETE_THIS_COMMENT___NEW_ITEMS', "#DO_NOT_DELETE_THIS_COMMENT___NEW_ITEMS".$content],
				]
			);

			$content = file_get_contents("$packageTemplatePath/repo/com_artiexample/admin/language/en-GB/en-GB.com_artiexample.sys.ini");
			$content = ArtiHelper::getContentBlock(
				$content, 
				'#DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS',
				'#DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS_END'
			);
			$content = ArtiHelper::replaceContent(
				$content,
				[
					['ARTIEXAMPLE', $packageNameInUpperCaseNoSpace],
					['EXAMPLENOTE', $viewNameInUpperCaseNoSpace],
					['Example Note', $viewNameInCapitalized],
				]
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationAdminPath/language/en-GB/en-GB.com_$packageNameInLowerCaseNoSpace.sys.ini",
				[
					['#DO_NOT_DELETE_THIS_COMMENT___NEW_ITEMS', "#DO_NOT_DELETE_THIS_COMMENT___NEW_ITEMS".$content],
				]
			);
		}
		if(($side === 'site') || ($side === 'both')){
			// Add CRUD views strings
			$content = file_get_contents("$packageTemplatePath/repo/com_artiexample/site/language/en-GB/en-GB.com_artiexample.ini");
			$content = ArtiHelper::getContentBlock(
				$content, 
				'#DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS',
				'#DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS_END'
			);
			$content = ArtiHelper::replaceContent(
				$content,
				[
					['ARTIEXAMPLE', $packageNameInUpperCaseNoSpace],
					['EXAMPLENOTE', $viewNameInUpperCaseNoSpace],
					['Example Note', $viewNameInCapitalized],
				]
			);
			ArtiHelper::replaceFileContent(
				"$packageInstallationSitePath/language/en-GB/en-GB.com_$packageNameInLowerCaseNoSpace.ini",
				[
					['#DO_NOT_DELETE_THIS_COMMENT___NEW_ITEMS', "#DO_NOT_DELETE_THIS_COMMENT___NEW_ITEMS".$content],
				]
			);
		}

		// Success
		Jom::message(Jom::translate('COM_ARTI_MESSAGE_PACKAGE_BUILD_SUCCESSFUL'));
		Jom::redirect("index.php?option=com_arti&view=package&id=$id");
    }
}


