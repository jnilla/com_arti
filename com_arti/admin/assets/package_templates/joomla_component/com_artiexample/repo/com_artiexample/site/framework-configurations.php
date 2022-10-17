<?php
defined('_JEXEC') or die;

$frameworkConfigurations = [
	'componentNameInPascalCase' => 'ArtiExample',
	"defaultViewNameInLowerCase" => "examplenotes",
	'ArtiExampleViewExampleNotes' => [
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


