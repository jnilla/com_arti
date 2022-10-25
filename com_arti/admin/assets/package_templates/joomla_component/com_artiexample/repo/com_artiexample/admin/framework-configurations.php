<?php
defined('_JEXEC') or die;

$frameworkConfigurations = [
	'componentNameInPascalCase' => 'ArtiExample',
	"defaultViewNameInLowerCase" => "examplenotes",
	'sidebarItemNamesInLowerCase' => [
		/*DO_NOT_DELETE_THIS_COMMENT___SIDEBAR_ITEMS*/
		'examplenotes',
	],
	/*DO_NOT_DELETE_THIS_COMMENT___NEW_ITEMS*/
	/*DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS*/
	'ArtiExampleViewExampleNotes' => [
		'toolbar' => [
			'iconClass' => 'icon-cube',
			'buttons' => [
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
				'field' => 'note',
			],
			[
				'field' => 'status',
			],
		],
		'defaultOrdering' => [
			'field' => 'id',
			'direction' => 'DESC',
		],
		'textSearchFields' => [
			'title',
			'note',
		],
	],
	'ArtiExampleViewExampleNote' => [
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
			]
		]
	],
	/*DO_NOT_DELETE_THIS_COMMENT___CRUD_VIEWS_END*/
];


