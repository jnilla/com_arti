<?php
defined('_JEXEC') or die;

$frameworkConfigurations = [
	'componentNameInPascalCase' => 'Arti',
	"defaultViewNameInLowerCase" => "packages",
	'sidebarItemNamesInLowerCase' => [
		'packages',
	],
	'ArtiViewPackages' => [
		'toolbar' => [
			'iconClass' => 'icon-cube', // Bootstrap icon class
			'buttons' => [ // List of toolbar buttons
				[
					'type' => 'add'
				],
				[
					'type' => 'delete'
				],
				[
					'type' => 'options'
				],
			],
		],
		'columns' => [
			[
				'field' => 'title',
				'addLink' => true
			],
			[
				'field' => 'description',
			],
			[
				'field' => 'type',
			],
			[
				'field' => 'name',
			],
		],
		'defaultOrdering' => [
			'field' => 'id',
			'direction' => 'DESC',
		],
		'textSearchFields' => [
			'title',
			'description',
		],
	],
	'ArtiViewPackage' => [
		'toolbar' => [
			'iconClass' => 'icon-cube',
			'buttons' => [
				[
					'type' => 'save'
				],
				[
					'type' => 'saveAndClose'
				],
				[
					'type' => 'saveToCopy'
				],
				[
					'type' => 'cancel'
				],
				[
					'type' => 'custom',
					'iconClass' => 'icon-play',
					'task' => 'package.initializepackage',
					'text' => 'COM_ARTI_BUTTON_INITIALIZE_PACKAGE',
					'listSelected' => false,
				],
			]
		]
	],
];

/*

make-installer
make-crud
make-view
make-view-layout
make-controller
make-model
make-helper

build
build-module
build-plugin


project_type
package_name_in_lower_case_snake_case
project_development_path
project_source_path
project_version



EXTENSION_NAME
PROJECT_PATH
DIST_VERSION



name
nameInLowerCase
nameInUpperCase
nameInLowerCaseNoSpace
nameInUpperCaseNoSpace
nameInPascalCase
nameInCamelCase
nameInSnakeCaseLowerCase
nameInSnakeCaseUpperCase
*/



