import { handleVariablesFor } from 'customizer-sync-helpers'

handleVariablesFor({
	cookieContentColor: [
		{
			selector: '.cookie-notification',
			variable: 'color',
			type: 'color:default'
		},

		{
			selector: '.cookie-notification',
			variable: 'colorHover',
			type: 'color:hover'
		}
	],

	cookieBackground: {
		selector: '.cookie-notification',
		variable: 'backgroundColor',
		type: 'color'
	},

	cookieButtonBackground: [
		{
			selector: '.cookie-notification',
			variable: 'buttonInitialColor',
			type: 'color:default'
		},

		{
			selector: '.cookie-notification',
			variable: 'buttonHoverColor',
			type: 'color:hover'
		}
	],

	cookieButtonText: [
		{
			selector: '.cookie-notification',
			variable: 'buttonTextInitialColor',
			type: 'color:default'
		},

		{
			selector: '.cookie-notification',
			variable: 'buttonTextHoverColor',
			type: 'color:hover'
		}
	],

	cookieSecondaryButtonBackground: [
		{
			selector: '.cookie-notification',
			variable: 'buttonSecondaryInitialColor',
			type: 'color:default'
		},

		{
			selector: '.cookie-notification',
			variable: 'buttonSecondaryHoverColor',
			type: 'color:hover'
		}
	],

	cookieSecondaryButtonText: [
		{
			selector: '.cookie-notification',
			variable: 'buttonSecondaryTextInitialColor',
			type: 'color:default'
		},

		{
			selector: '.cookie-notification',
			variable: 'buttonSecondaryTextHoverColor',
			type: 'color:hover'
		}
	],

	cookieBorderColor: [
		{
			selector: '.cookie-notification',
			variable: 'borderColor',
			type: 'color:default'
		},
	],

	cookieMaxWidth: {
		selector: '.cookie-notification',
		variable: 'maxWidth',
		unit: 'px'
	}
})
