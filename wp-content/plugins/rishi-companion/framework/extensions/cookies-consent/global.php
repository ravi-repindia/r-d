<?php

// Content color
rishi__cb_customizer_output_colors ( [
	'value' => get_theme_mod('cookieContentColor'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor1)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification',
			'variable' => 'color'
		],
	],
]);

// Primary Button color
rishi__cb_customizer_output_colors( [
	'value' => get_theme_mod('cookieButtonBackground'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor5)' ],
		'hover' => [ 'color' => 'var(--paletteColor3)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification',
			'variable' => 'buttonInitialColor'
		],

		'hover' => [
			'selector' => '.cookie-notification',
			'variable' => 'buttonHoverColor'
		]
	],
]);

// Primary Button Text color
rishi__cb_customizer_output_colors( [
	'value' => get_theme_mod('cookieButtonText'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor3)' ],
		'hover' => [ 'color' => 'var(--paletteColor5)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification',
			'variable' => 'buttonTextInitialColor'
		],

		'hover' => [
			'selector' => '.cookie-notification',
			'variable' => 'buttonTextHoverColor'
		]
	],
]);


// Secondary Button color
rishi__cb_customizer_output_colors( [
	'value' => get_theme_mod('cookieSecondaryButtonBackground'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor3)' ],
		'hover' => [ 'color' => 'var(--paletteColor5)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification',
			'variable' => 'buttonSecondaryInitialColor'
		],

		'hover' => [
			'selector' => '.cookie-notification',
			'variable' => 'buttonSecondaryHoverColor'
		]
	],
]);

// Secondary Button Text color
rishi__cb_customizer_output_colors( [
	'value' => get_theme_mod('cookieSecondaryButtonText'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor5)' ],
		'hover' => [ 'color' => 'var(--paletteColor3)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification',
			'variable' => 'buttonSecondaryTextInitialColor'
		],

		'hover' => [
			'selector' => '.cookie-notification',
			'variable' => 'buttonSecondaryTextHoverColor'
		]
	],
]);


// Background color
rishi__cb_customizer_output_colors ([
	'value' => get_theme_mod('cookieBackground'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor5)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification',
			'variable' => 'backgroundColor'
		],
	],
]);

// Border color
rishi__cb_customizer_output_colors ([
	'value' => get_theme_mod('cookieBorderColor'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor3)' ],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification',
			'variable' => 'borderColor'
		],
	],
]);

// Content color
rishi__cb_customizer_output_colors ( [
	'value' => get_theme_mod('cookieLink'),
	'default' => [
		'default' => [ 'color' => 'var(--paletteColor1)' ],
		'hover' => [
			'color' => 'var(--paletteColor3)',
		],
	],
	'css' => $css,
	'variables' => [
		'default' => [
			'selector' => '.cookie-notification',
			'variable' => 'colorLink'
		],

		'hover' => [
			'selector' => '.cookie-notification',
			'variable' => 'colorLinkHover'
		]
	],
]);

$cookieMaxWidth = get_theme_mod( 'cookieMaxWidth', 455 );
$css->put(
	'.cookie-notification',
	'--maxWidth: ' . $cookieMaxWidth . 'px'
);

