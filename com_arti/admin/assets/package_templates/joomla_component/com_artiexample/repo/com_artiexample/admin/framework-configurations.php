<?php
defined('_JEXEC') or die;

$frameworkConfigurations = [
	'componentNameInPascalCase' => 'ArtiExample',
	"defaultViewNameInLowerCase" => "examplenotes",
	'sidebarItemNamesInLowerCase' => [
		'examplenotes',

		/*DO_NOT_DELETE_THIS_COMMENT_01*/
		
	],
	
	/*DO_NOT_DELETE_THIS_COMMENT_02*/
	
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
];


