<?php
defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\Folder as JFolder;
use Joomla\CMS\Filesystem\File as JFile;

/**
 * Component helper class
 */
class ArtiHelper{

	/**
	 * Initialize the package
	 *
	 * @param array $package
	 * 		Package data
	 *
	 * @return Void
	 */
	public static function initializePackage($package){
		// Prepare the package data
		$package['name_variations'] = self::generateNameVariations($package['name']);
		$package['update_server_url'] = trim($package['update_server_url']);
		$package['update_server_url'] = empty($package['update_server_url']) ? 'https://example.com/update-server/example/' : $package['update_server_url'];

		// Define package type
		switch ($package['type']) {
			case 'joomla_component':
				$packagePrefix = 'com_';
				break;
			// case 'joomla_component':
			// 	$packagePrefix = 'com_';
			// 	break;
		}

		// Generate the package path
		$package['package_path'] = $package['project_path'].'/'.$packagePrefix.$package['name_variations']['nameInNoSpaceLowerCase'];

		// Abort if package folder already exist
		if(file_exists($package['package_path'])){
			Jom::message(Jom::translate('COM_ARTI_ERROR_PACKAGE_ALREADY_EXIST').$package['package_path'], 'error');
			return;
		}

		// Create project folder
		@mkdir($package['project_path'], 0777, true);

		// Process the package template
		switch ($package['type']) {
			case 'joomla_component':
				self::initializeJoomlaComponentPackage($package);
				break;
			// case 'joomla_component':
			// 	$packagePrefix = 'com_';
			// 	break;
		}
		
		// Success
		Jom::message(Jom::translate('COM_ARTI_MESSAGE_PACKAGE_INITIALIZED').$package['package_path']);
	}


	/**
	 * Rename files and folders resursively
	 *
	 * @param array $path
	 * 		Folder path
	 * @param array $search
	 * 		Search text
	 * @param array $replace
	 * 		Text to replace with
	 *
	 * @return void
	 */
	public static function renameFilesAndFoldersRecursively($path, $search, $replace){
		// List files		
		$files = JFolder::files($path, '', true, true);

		// Rename files
		foreach ($files as $fileKey => $file){
			$name1 = basename($file);
			if(strpos($name1, $search) !== false){	
				$name2 = str_replace($search, $replace, $name1);
				$newName = dirname($file)."/$name2";
				rename($file, $newName);
				$files[$fileKey] = $newName;
			}
		}

		// List folders		
		$folders = JFolder::folders($path, '', true, true);
		arsort($folders);

		// Rename folders
		foreach ($folders as $folderKey => $folder){
			$name1 = basename($folder);
			if(strpos($name1, $search) !== false){	
				$name2 = str_replace($search, $replace, $name1);
				$newName = dirname($folder)."/$name2";
				rename($folder, $newName);
				$folders[$folderKey] = $newName;
			}
		}
	}


	/**
	 * Initialize a Joomla component package
	 *
	 * @param array $package
	 * 		Package data
	 *
	 * @return Void
	 */
	public static function initializeJoomlaComponentPackage($package){
		// Copy package template
		JFolder::copy(JPATH_COMPONENT_ADMINISTRATOR."/assets/package_templates/joomla_component/com_artiexample", $package['package_path']);

		// Rename files and folders with "artiexample"
		self::renameFilesAndFoldersRecursively($package['package_path'], 'artiexample', $package['name_variations']['nameInNoSpaceLowerCase']);

		// Rename files and folders with "ArtiExample"
		self::renameFilesAndFoldersRecursively($package['package_path'], 'ArtiExample', $package['name_variations']['nameInNoSpaceLowerCase']);

		// Replace files content
		$files = JFolder::files($package['package_path'], '', true, true);
		$replacements = [
			['readme_package_title', $package['title']],
			['readme_package_description', $package['description']],
			['readme_package_name', 'com_'.$package['name_variations']['nameInNoSpaceLowerCase']],
			['<author>jnilla.com</author>', '<author>'.$package['author'].'</author>'],
			['<copyright>Copyright (C) 2022 jnilla.com. All rights reserved.</copyright>', '<copyright>Copyright (C) '.date('Y').' '.$package['author'].'. All rights reserved.</copyright>'],
			['<creationDate>2022-10-10</creationDate>', '<creationDate>'.date('Y-m-d').'</creationDate>'],
			['<authorEmail>dev@jnilla.com</authorEmail>', '<authorEmail>'.$package['author_email'].'</authorEmail>'],
			['<authorUrl>jnilla.com</authorUrl>', '<authorUrl>'.$package['author_url'].'</authorUrl>'],
			['<server type="extension" priority="1" name="Arti Example">https://www.jnilla.com/update-server/com_artiexample/updates.xml</server>', '<server type="extension" priority="1" name="'.$package['title'].'">'.$package['update_server_url'].'</server>'],
			['"Arti Example"', '"'.$package['title'].'"'],
			['"Arti Example Component"', '"'.$package['description'].'"'],
			['"Arti Example: Options"', '"'.$package['title'].': Options"'],
			// ['xxxxxx', 'xxxxxx'],
			// ['xxxxxx', 'xxxxxx'],
			// ['xxxxxx', 'xxxxxx'],
			// ['xxxxxx', 'xxxxxx'],
			// ['xxxxxx', 'xxxxxx'],
			// ['xxxxxx', 'xxxxxx'],
			// ['xxxxxx', 'xxxxxx'],
			// ['xxxxxx', 'xxxxxx'],
			['ArtiExample', $package['name_variations']['nameInPascalCase']],
			['ARTIEXAMPLE', $package['name_variations']['nameInNoSpaceUpperCase']],
			['artiexample', $package['name_variations']['nameInNoSpaceLowerCase']],
		];
		foreach ($files as $file){
			self::replaceFileContent($file, $replacements);
		}

		// DEBUG >>>
		ob_start(); var_dump($package);        $phpdump = ob_get_clean(); $phptrace = '<span style=\'color:purple\'>'.str_replace(@JPATH_ROOT, '.', dirname(__FILE__)).'/<strong>'.basename(__FILE__).'</strong></span><span style=\'color:green\'>:'.__LINE__.'</span> cs:<span style=\'color:blue\'>'.@__CLASS__.'</span> fn:<span style=\'color:red\'>'.__FUNCTION__.'()</span>'; echo "<pre>PDD &gt;&gt;&gt;:\n$phptrace\nDUMP:\n$phpdump\n &lt;&lt;&lt; PDD</pre>";
		die;
		// <<< DEBUG
	}

	/**
	 * Replace file content
	 *
	 * @param String $path 
	 * 		File path
	 * @param Array $replacements
	 * 		Search and replace text pairs
	 *
	 * @return Array
	 */
	public static function replaceFileContent($path, $replacements){
		$time = microtime(true);
		$isMatch = false;

		// Get file content
		$content = file_get_contents($path);
		
		// Prepare placeholders
		foreach($replacements as $replacementKey => $replacement){
			$search = $replacement[0];
			$placeholder = "@@-placeholder-$replacementKey-$time-@@";
			$search = preg_quote($search, '/');
			$content = preg_replace('/'.$search.'/', $placeholder, $content, -1, $count);

			if($count > 0){
				$isMatch = true;
			}
		}

		// Exit if there is no matches
		if(!$isMatch){
			return;
		}

		// Replace placeholders
		foreach($replacements as $replacementKey => $replacement){
			$replace = $replacement[1];
			$placeholder = "@@-placeholder-$replacementKey-$time-@@";
			$content = str_replace($placeholder, $replace, $content);
		}

		// Update file content
		file_put_contents($path, $content);
	}

	/**
	 * Get package data by id
	 *
	 * @param integer $id
	 *     Package id
	 *
	 * @return array
	 *     Package data
	 */
	public static function getPackage($id){
		return Jom::selectOne(
			"SELECT *
			FROM #__arti_packages
			WHERE id = :id",
			['id' => $id]
		);
	}

	/**
	 * Generate name variations
	 *
	 * @param String $nameInSnakeCaseLowerCase
	 * 		Name in snake case lower case
	 *
	 * @return Array Name variations
	 */
	public static function generateNameVariations($nameInSnakeCaseLowerCase){
		$variations = [];
		$variations["nameInSnakeCaseLowerCase"] = strtolower($nameInSnakeCaseLowerCase);
		$variations["nameInSnakeCaseUpperCase"] = strtoupper($nameInSnakeCaseLowerCase);

		$variations["nameInKebabCaseLowerCase"] = str_replace('_', '-', $variations["nameInSnakeCaseLowerCase"]);
		$variations["nameInKebabCaseUpperCase"] = strtoupper($variations["nameInKebabCaseLowerCase"]);

		$variations["nameInCapitalized"] = ucwords(str_replace('_', ' ', $variations["nameInSnakeCaseLowerCase"]));
		$variations["nameInPascalCase"] = str_replace(' ', '', $variations["nameInCapitalized"]);
		$variations["nameInCamelCase"] = lcfirst($variations["nameInPascalCase"]);

		$variations["nameInNoSpaceLowerCase"] = strtolower($variations["nameInCamelCase"]);
		$variations["nameInNoSpaceUpperCase"] = strtoupper($variations["nameInCamelCase"]);

		return $variations;
	}
}


