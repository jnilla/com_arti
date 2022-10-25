<?php
defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\Folder as JFolder;
use Joomla\CMS\Filesystem\File as JFile;
use Jnilla\Jom\Jom as Jom;

/**
 * Component helper class
 */
class ArtiHelper{

	/**
	 * Rename files and folders resursively
	 *
	 * @param array $path
	 * 		Folder path
	 * @param string $search
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
		$packageNameVariations = ArtiHelper::generateNameVariations('packageName', $package['name']);
		extract($packageNameVariations);

		$packageTemplatePath = JPATH_COMPONENT_ADMINISTRATOR."/assets/package_templates/joomla_component/com_artiexample";
		$packagePath = $package['package_path'];

		// Copy package template
		JFolder::copy($packageTemplatePath, $packagePath);

		// Rename files and folders with "artiexample"
		self::renameFilesAndFoldersRecursively($packagePath, 'artiexample', $packageNameInLowerCaseNoSpace);

		// Rename files and folders with "ArtiExample"
		self::renameFilesAndFoldersRecursively($packagePath, 'ArtiExample', $packageNameInLowerCaseNoSpace);

		// Replace files content
		$files = JFolder::files($packagePath, '', true, true);
		$replacements = [
			['readme_package_title', $package['title']],
			['readme_package_description', $package['description']],
			['readme_package_name', "com_$packageNameInLowerCaseNoSpace"],
			['<author>exampleauthor</author>', '<author>'.$package['author'].'</author>'],
			['<copyright>Copyright (C) 0000 exampleauthor. All rights reserved.</copyright>', '<copyright>Copyright (C) '.date('Y').' '.$package['author'].'. All rights reserved.</copyright>'],
			['<creationDate>0000-00-00</creationDate>', '<creationDate>'.date('Y-m-d').'</creationDate>'],
			['<authorEmail>example@example.com</authorEmail>', '<authorEmail>'.$package['author_email'].'</authorEmail>'],
			['<authorUrl>https://example.com</authorUrl>', '<authorUrl>'.$package['author_url'].'</authorUrl>'],
			['<version>0.0.0</version>', '<version>'.$package['version'].'</version>'],
			['<server type="extension" priority="1" name="Arti Example">https://www.example.com/update-server/com_artiexample/updates.xml</server>', '<server type="extension" priority="1" name="'.$package['title'].'">'.$package['update_server_manifest_url'].'</server>'],
			['"Arti Example"', '"'.$package['title'].'"'],
			['"Arti Example Component"', '"'.$package['description'].'"'],
			['"Arti Example: Options"', '"'.$package['title'].': Options"'],
			['update_file_package_title', $package['title']],
			['update_file_package_description', $package['description']],
			['update_file_package_filename', "com_$packageNameInLowerCaseNoSpace"],
			['/*DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS_END*/', ''],
			['/*DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS*/', ''],
			['#DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS_END', ''],
			['#DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS', ''],
			// ['xxxxxx', 2222222],
			// ['xxxxxx', 2222222],
			// ['xxxxxx', 2222222],
			['ArtiExample', $packageNameInPascalCase],
			['ARTIEXAMPLE', $packageNameInUpperCaseNoSpace],
			['artiexample', $packageNameInLowerCaseNoSpace],
		];
		foreach ($files as $file){
			self::replaceFileContent($file, $replacements);
		}

		// Create starter package
		$starterPackagePath = "$packagePath/dist/com_$packageNameInLowerCaseNoSpace-v(STARTER).zip";
		Jom::mkdir($packagePath.'/dist/');
		Jom::zip("$packagePath/repo/com_$packageNameInLowerCaseNoSpace", $starterPackagePath);
		Jom::message(Jom::translate('COM_ARTI_MESSAGE_STARTER_PACKAGE_LOCATION').$starterPackagePath);
	}

	/**
	 * Build a Joomla component package
	 *
	 * @param array $package
	 * 		Package data
	 *
	 * @return Void
	 */
	public static function buildJoomlaComponentPackage($package){
		$packageNameVariations = ArtiHelper::generateNameVariations('packageName', $package['name']);
		extract($packageNameVariations);

		$packageTemplatePath = JPATH_COMPONENT_ADMINISTRATOR."/assets/package_templates/joomla_component/com_artiexample";
		$packagePath = $package['package_path'];

		// Delete package repo package folder
		JFolder::delete("$packagePath/repo/com_$packageNameInLowerCaseNoSpace");

		// Copy admin folder
		JFolder::copy(
			JPATH_SITE."/administrator/components/com_$packageNameInLowerCaseNoSpace",
			$packagePath."/repo/com_$packageNameInLowerCaseNoSpace/admin",
		);

		// Move manifest file
		JFile::move(
			$packagePath."/repo/com_$packageNameInLowerCaseNoSpace/admin/$packageNameInLowerCaseNoSpace.xml",
			$packagePath."/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml"
		);

		// Copy site folder
		JFolder::copy(
			JPATH_SITE."/components/com_$packageNameInLowerCaseNoSpace",
			$packagePath."/repo/com_$packageNameInLowerCaseNoSpace/site",
		);

		// Copy media folder
		JFolder::copy(
			JPATH_SITE."/media/com_$packageNameInLowerCaseNoSpace",
			$packagePath."/repo/com_$packageNameInLowerCaseNoSpace/media",
		);
		
		// Update manifest file

		// Update <author> block
		self::replaceFileContentBlock(
			"$packagePath/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml",
			'<author>',
			'</author>',
			$package['author']
		);

		// Update <creationDate> block
		self::replaceFileContentBlock(
			"$packagePath/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml",
			'<creationDate>',
			'</creationDate>',
			date('Y-m-d')
		);

		// Update <copyright> block
		self::replaceFileContentBlock(
			"$packagePath/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml",
			'<copyright>',
			'</copyright>',
			'Copyright (C) '.date('Y').' '.$package['author'].'. All rights reserved.'
		);

		// Update <authorEmail> block
		self::replaceFileContentBlock(
			"$packagePath/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml",
			'<authorEmail>',
			'</authorEmail>',
			$package['author_email']
		);

		// Update <authorUrl> block
		self::replaceFileContentBlock(
			"$packagePath/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml",
			'<authorUrl>',
			'</authorUrl>',
			$package['author_url']
		);

		// Update <version> block
		self::replaceFileContentBlock(
			"$packagePath/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml",
			'<version>',
			'</version>',
			$package['version']
		);

		// Update <files folder="admin"> block
		$content = [];
		$content[] = '';
		$items = JFolder::files("$packagePath/repo/com_$packageNameInLowerCaseNoSpace/admin");
		foreach ($items as $item) {
			$content[] = "\t\t\t<filename>$item</filename>";
		}
		$items = JFolder::folders("$packagePath/repo/com_$packageNameInLowerCaseNoSpace/admin");
		foreach ($items as $item) {
			$content[] = "\t\t\t<folder>$item</folder>";
		}
		$content[] = "\t\t";
		$content = implode("\n", $content);

		self::replaceFileContentBlock(
			"$packagePath/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml",
			'<files folder="admin">',
			'</files>',
			$content
		);
		
		// Update <files folder="site"> block
		$content = [];
		$content[] = '';
		$items = JFolder::files("$packagePath/repo/com_$packageNameInLowerCaseNoSpace/site");
		foreach ($items as $item) {
			$content[] = "\t\t<filename>$item</filename>";
		}
		$items = JFolder::folders("$packagePath/repo/com_$packageNameInLowerCaseNoSpace/site");
		foreach ($items as $item) {
			$content[] = "\t\t<folder>$item</folder>";
		}
		$content[] = "\t";
		$content = implode("\n", $content);

		self::replaceFileContentBlock(
			"$packagePath/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml",
			'<files folder="site">',
			'</files>',
			$content
		);

		// Update <media destination="com_example" folder="media"> block
		$content = [];
		$content[] = '';
		$items = JFolder::files("$packagePath/repo/com_$packageNameInLowerCaseNoSpace/media");
		foreach ($items as $item) {
			$content[] = "\t\t<filename>$item</filename>";
		}
		$items = JFolder::folders("$packagePath/repo/com_$packageNameInLowerCaseNoSpace/media");
		foreach ($items as $item) {
			$content[] = "\t\t<folder>$item</folder>";
		}
		$content[] = "\t";
		$content = implode("\n", $content);

		self::replaceFileContentBlock(
			"$packagePath/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml",
			'<media destination="com_'.$packageNameInLowerCaseNoSpace.'" folder="media">',
			'</media>',
			$content
		);

		// Update <version> block
		self::replaceFileContentBlock(
			"$packagePath/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml",
			'<version>',
			'</version>',
			$package['version']
		);

		// Update <updateservers> block
		$content = [];
		$content[] = '';
		$content[] = "\t\t".'<server type="extension" priority="1" name="'.$package['title'].'">'.$package['update_server_manifest_url'].'</server>';
		$content[] = "\t";
		$content = implode("\n", $content);

		self::replaceFileContentBlock(
			"$packagePath/repo/com_$packageNameInLowerCaseNoSpace/$packageNameInLowerCaseNoSpace.xml",
			'<updateservers>',
			'</updateservers>',
			$content
		);

		// Update SQL install script

		// List related tables
		$dbPrefix = Jom::conf('dbprefix');
		$tables = [];
		foreach (Jom::db()->getTableList() as $table) {
			if(preg_match('/^'.$dbPrefix.$packageNameInLowerCaseNoSpace.'_/', $table)){
				$tables[] = $table;
			}
		}

		// Generate CREATE TABLE scripts
		$content = [];
		$content[] = "";
		foreach ($tables as $table) {
			$script = Jom::query("SHOW CREATE TABLE `$table`")[0]['Create Table'].';';
			$script = preg_replace("/CREATE TABLE/", "CREATE TABLE IF NOT EXISTS", $script, 1);
			$script = preg_replace("/$dbPrefix/", "#__", $script, 1);
			$content[] = $script;
			$content[] = "\n\n\n";
		}
		$content = implode("\n", $content);

		// Save SQL script to file
		file_put_contents("$packagePath/repo/com_$packageNameInLowerCaseNoSpace/admin/sql/install.mysql.utf8.sql", $content);

		// Create package installer
		$packageInstallerFilename = "com_$packageNameInLowerCaseNoSpace-v".$package['version'].".zip";
		$packageInstallerPath = "$packagePath/dist/$packageInstallerFilename";
		Jom::mkdir("$packagePath/dist/");
		Jom::zip("$packagePath/repo/com_$packageNameInLowerCaseNoSpace", $packageInstallerPath);
		Jom::message(Jom::translate('COM_ARTI_MESSAGE_PACKAGE_INSTALLER_LOCATION').$packageInstallerPath);

		// Update update-server folder

		// Move package installer
		JFile::copy(
			$packageInstallerPath, 
			"$packagePath/update-server/com_$packageNameInLowerCaseNoSpace/$packageInstallerFilename"
		);
		
		// Update updates.xml file
		self::replaceFileContentBlock(
			"$packagePath/update-server/com_$packageNameInLowerCaseNoSpace/updates.xml",
			'<version>',
			'</version>',
			$package['version']
		);

		$content = [];
		$content[] = "\n\t\t\t".'<downloadurl type="full" format="zip">'.$package['update_server_base_url']."com_$packageNameInLowerCaseNoSpace/$packageInstallerFilename".'</downloadurl>';
		$content[] = "\t\t";
		$content = implode("\n", $content);
		self::replaceFileContentBlock(
			"$packagePath/update-server/com_$packageNameInLowerCaseNoSpace/updates.xml",
			'<downloads>',
			'</downloads>',
			$content
		);
	}

	/**
	 * Replace a file content block defined by two markers
	 * 
	 * Note: Just the content between the markers is replaced, 
	 * the markers themselves are excluded from the replacement operation.
	 *
	 * @param string $path 
	 * 		File path
	 * @param string $startMarker
	 * 		Block starting marker
	 * @param string $endMarker
	 * 		Block ending marker
	 * @param string $replacement
	 * 		Replacement text
	 *
	 * @return Array
	 */
	public static function replaceFileContentBlock($path, $startMarker, $endMarker, $replacement){
		$time = microtime(true);
		$isMatch = false;

		// Get file content
		$content = file_get_contents($path);
		
		// Prepare data
		$replacement = $startMarker.$replacement.$endMarker;
		$startMarker = preg_quote($startMarker, '/');
		$endMarker = preg_quote($endMarker, '/');

		// Apply replacement
		$content = preg_replace('/'.$startMarker.'.*?'.$endMarker.'/s', $replacement, $content, -1, $isMatch);
		$isMatch = !!$isMatch;

		// Exit if there is no matches
		if(!$isMatch){
			return;
		}

		// Update file content
		file_put_contents($path, $content);
	}

	/**
	 * Get a content block defined by two markers
	 * 
	 * Note: Just the content between the markers is returned, 
	 * the markers themselves are excluded.
	 *
	 * @param string $content 
	 * 		Content to work with
	 * @param string $startMarker
	 * 		Block starting marker
	 * @param string $endMarker
	 * 		Block ending marker
	 *
	 * @return Array
	 */
	public static function getContentBlock($content, $startMarker, $endMarker){
		// Prepare data
		$startMarker = preg_quote($startMarker, '/');
		$endMarker = preg_quote($endMarker, '/');

		// Get block
		preg_match('/'.$startMarker.'(.*?)'.$endMarker.'/s', $content, $content);

		return $content[1];
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
	 * Replace content
	 *
	 * @param String $content 
	 * 		Content to work with
	 * @param Array $replacements
	 * 		Search and replace text pairs
	 *
	 * @return string
	 *     New content
	 */
	public static function replaceContent($content, $replacements){
		$time = microtime(true);

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

		// Replace placeholders
		foreach($replacements as $replacementKey => $replacement){
			$replace = $replacement[1];
			$placeholder = "@@-placeholder-$replacementKey-$time-@@";
			$content = str_replace($placeholder, $replace, $content);
		}

		return $content;
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
		return Jom::queryOne(
			"SELECT *
			FROM #__arti_packages
			WHERE id = :id",
			['id' => $id]
		);
	}

	/**
	 * Generate name variations
	 *
	 * @param String $prefix
	 *     Variation key name prefix
	 * @param String $nameInLowerCaseSnakeCase
	 * 	   Name in lower case snake case
	 *
	 * @return Array 
	 *     Name variations
	 */
	public static function generateNameVariations($prefix, $nameInLowerCaseSnakeCase){
		$variations = [];

		$variations[$prefix."InLowerCaseSnakeCase"] = strtolower($nameInLowerCaseSnakeCase);
		$variations[$prefix."InUpperCaseSnakeCase"] = strtoupper($nameInLowerCaseSnakeCase);

		$variations[$prefix."InLowerCaseKebabCase"] = str_replace('_', '-', $variations[$prefix."InLowerCaseSnakeCase"]);
		$variations[$prefix."InUpperCaseKebabCase"] = strtoupper($variations[$prefix."InLowerCaseKebabCase"]);

		$variations[$prefix."InCapitalized"] = ucwords(str_replace('_', ' ', $variations[$prefix."InLowerCaseSnakeCase"]));
		$variations[$prefix."InPascalCase"] = str_replace(' ', '', $variations[$prefix."InCapitalized"]);
		$variations[$prefix."InCamelCase"] = lcfirst($variations[$prefix."InPascalCase"]);

		$variations[$prefix."InLowerCaseNoSpace"] = strtolower($variations[$prefix."InCamelCase"]);
		$variations[$prefix."InUpperCaseNoSpace"] = strtoupper($variations[$prefix."InCamelCase"]);

		return $variations;
	}
}


