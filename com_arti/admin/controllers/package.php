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
}


