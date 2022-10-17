<?php
defined('_JEXEC') or die();

use Jnilla\Lara\Controller\AdminItem as AdminItemController;

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
			Jom::redirect();
		}

		// Initialize the package
		$package = ArtiHelper::getPackage($id);
		ArtiHelper::initializePackage($package);
		
		Jom::redirect();
    }
}


