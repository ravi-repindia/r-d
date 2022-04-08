<?php

$options = [
	'title' => __('Cookie Consent', 'rishi-companion'),
	'container' => [ 'priority' => 8 ],
	'options' => [

		'cookie_consent_section_options' => [
			'type' => 'rt-options',
			'setting' => [ 'transport' => 'postMessage' ],
			'inner-options' => [
				rishi__cb_customizer_rand_md5() => [
					'type' => 'rt-title',
					'label' => __( 'Cookie Consent', 'rishi-companion' ),
				],

				rishi__cb_customizer_rand_md5() => [
					'title' => __( 'General', 'rishi-companion' ),
					'type' => 'tab',
					'options' => [

						'cookie_consent_type' => [
							'label' => false,
							'type' => 'rt-image-picker',
							'value' => 'type-1',
							'setting' => [ 'transport' => 'postMessage' ],
							'choices' => [

								'type-1' => [
									'src'   => plugin_dir_url(RISHI_COMPANION_PLUGIN_FILE) . 'framework/extensions/cookies-consent/images/type-1.svg',
									'title' => __( 'Type 1', 'rishi-companion' ),
								],

								'type-2' => [
									'src'   => plugin_dir_url(RISHI_COMPANION_PLUGIN_FILE) . 'framework/extensions/cookies-consent/images/type-2.svg',
									'title' => __( 'Type 2', 'rishi-companion' ),
								],

							],
						],			

			            rishi__cb_customizer_rand_md5() => [
							'type'      => 'rt-condition',
							'condition' => [ 'cookie_consent_type' => 'type-1' ],
							'options'   => [                    
			                    'cookie_consent_type_one' => [
								'label'   => __('Position', 'rishi-companion'),
								'type'    => 'rt-radio',
								'value'   => 'left',
								'setting' => [ 'transport' => 'postMessage' ],
								'view'    => 'text',
									'choices' => [
										'left'   => __('Left', 'rishi-companion'),
										'right'  => __('Right', 'rishi-companion'),
									],
								],
			                ],
							'setting' => [ 'transport' => 'postMessage' ],
			            ],

			            rishi__cb_customizer_rand_md5() => [
							'type'      => 'rt-condition',
							'condition' => [ 'cookie_consent_type' => 'type-2' ],
							'options'   => [                    
			                    'cookie_consent_type_two' => [
								'label'   => __('Position', 'rishi-companion'),
								'type'    => 'rt-radio',
								'value'   => 'bottom',
								'setting' => [ 'transport' => 'postMessage' ],
								'view'    => 'text',
									'choices' => [
										'top'   => __('Top', 'rishi-companion'),
										'bottom'  => __('Bottom', 'rishi-companion'),
									],
								],
			                ],
							'setting' => [ 'transport' => 'postMessage' ],
			            ],

						'cookie_consent_period' => [
							'label' => __('Cookie period', 'rishi-companion'),
							'type' => 'rt-select',
							'value' => 'forever',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],
							'choices' => rishi__cb_customizer_ordered_keys(

								[
									'onehour' => __( 'One hour', 'rishi-companion' ),
									'oneday' => __( 'One day', 'rishi-companion' ),
									'oneweek' => __( 'One week', 'rishi-companion' ),
									'onemonth' => __( 'One month', 'rishi-companion' ),
									'threemonths' => __( 'Three months', 'rishi-companion' ),
									'sixmonths' => __( 'Six months', 'rishi-companion' ),
									'oneyear' => __( 'One year', 'rishi-companion' ),
									'forever' => __('Forever', 'rishi-companion')
								]

							),
						],

						'cookie_consent_content' => [
							'label' => __( 'Content', 'rishi-companion' ),
							'type' => 'wp-editor',
							'value' => __('We use cookies on our website to give you the most relevant experience. By continuing to use the site, you agree to the use of cookies.', 'rishi-companion'),
							'disableRevertButton' => true,
							'setting' => [ 'transport' => 'postMessage' ],

							'quicktags' => false,
							'mediaButtons' => false,
							'tinymce' => [
								'toolbar1' => 'bold,italic,link,alignleft,aligncenter,alignright,undo,redo',
							],
						],

						'cookie_consent_button_text' => [
							'label' => __( 'Accept Button text', 'rishi-companion' ),
							'type' => 'text',
							'design' => 'block',
							'value' => __('Accept', 'rishi-companion'),
							'sync'   => array(
								'selector' => '.cookie-notification .rt-accept',
								'render'   => function() {
									echo rishi_companion_get_cc_accept_button_info(); 
								},
							),
						],

						'cookie_consent_button_two_text' => [
							'label' => __( 'Decline Button text', 'rishi-companion' ),
							'type' => 'text',
							'design' => 'block',
							'value' => __('Decline', 'rishi-companion'),
							'sync'   => array(
								'selector' => '.cookie-notification .ct-decline-close',
								'render'   => function() {
									echo rishi_companion_get_cc_decline_button_info(); 
								},
							),
						],

					],
				],

				rishi__cb_customizer_rand_md5() => [
					'title' => __( 'Design', 'rishi-companion' ),
					'type' => 'tab',
					'options' => [

						'cookieContentColor' => [
							'label' => __( 'Font Color', 'rishi-companion' ),
							'type'  => 'rt-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => 'var(--paletteColor1)',
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'rishi-companion' ),
									'id' => 'default',
									'inherit' => 'var(--color)'
								],
							],
						],

						'cookieLink' => [
							'label' => __( 'Link Color', 'rishi-companion' ),
							'type'  => 'rt-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => 'var(--paletteColor3)',
								],
								'hover' => [
									'color' => 'var(--paletteColor2)',
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'rishi-companion' ),
									'id' => 'default',
								],
								[
									'title' => __( 'Hover', 'rishi-companion' ),
									'id' => 'hover',
									'inherit' => 'var(--colorHover)'
								],
							],
						],

						'cookieButtonBackground' => [
							'label' => __( 'Primary Button Color', 'rishi-companion' ),
							'type'  => 'rt-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],
							'value' => [
								'default' => [
									'color' => 'var(--paletteColor5)',
								],

								'hover' => [
									'color' => 'var(--paletteColor3)',
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'rishi-companion' ),
									'id' => 'default',
								],

								[
									'title' => __( 'Hover', 'rishi-companion' ),
									'id' => 'hover',
								],
							],
						],

						'cookieButtonText' => [
							'label' => __( 'Primary Button Text Color', 'rishi-companion' ),
							'type'  => 'rt-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],
							'value' => [
								'default' => [
									'color' => 'var(--paletteColor3)',
								],

								'hover' => [
									'color' => 'var(--paletteColor5)',
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'rishi-companion' ),
									'id' => 'default',
								],

								[
									'title' => __( 'Hover', 'rishi-companion' ),
									'id' => 'hover',
								],
							],
						],

						'cookieSecondaryButtonBackground' => [
							'label' => __( 'Secondary Button Color', 'rishi-companion' ),
							'type'  => 'rt-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],
							'value' => [
								'default' => [
									'color' => 'var(--paletteColor3)',
								],

								'hover' => [
									'color' => 'var(--paletteColor5)',
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'rishi-companion' ),
									'id' => 'default',
								],

								[
									'title' => __( 'Hover', 'rishi-companion' ),
									'id' => 'hover',
								],
							],
						],

						'cookieSecondaryButtonText' => [
							'label' => __( 'Secondary Button Text Color', 'rishi-companion' ),
							'type'  => 'rt-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],
							'value' => [
								'default' => [
									'color' => 'var(--paletteColor5)',
								],

								'hover' => [
									'color' => 'var(--paletteColor3)',
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'rishi-companion' ),
									'id' => 'default',
								],

								[
									'title' => __( 'Hover', 'rishi-companion' ),
									'id' => 'hover',
								],
							],
						],

						'cookieBorderColor' => [
							'label' => __( 'Border Color', 'rishi-companion' ),
							'type'  => 'rt-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => 'var(--paletteColor3)',
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'rishi-companion' ),
									'id' => 'default',
								],
							],
						],

						'cookieBackground' => [
							'label' => __( 'Background Color', 'rishi-companion' ),
							'type'  => 'rt-color-picker',
							'design' => 'inline',
							'setting' => [ 'transport' => 'postMessage' ],

							'value' => [
								'default' => [
									'color' => 'var(--paletteColor5)',
								],
							],

							'pickers' => [
								[
									'title' => __( 'Initial', 'rishi-companion' ),
									'id' => 'default',
								],
							],
						],

						rishi__cb_customizer_rand_md5() => [
							'type' => 'rt-condition',
							'condition' => [ 'cookie_consent_type' => 'type-1' ],
							'options' => [

								'cookieMaxWidth' => [
									'label' => __( 'Maximum Width', 'rishi-companion' ),
									'type' => 'rt-slider',
									'value' => 455,
									'min' => 200,
									'max' => 500,
									'setting' => [ 'transport' => 'postMessage' ],
								],

							],
						],

					],
				],
			],
		],
	],
];

