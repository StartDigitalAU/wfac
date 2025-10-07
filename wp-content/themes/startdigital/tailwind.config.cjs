module.exports = {
	content: ['./*/*.php', './*.php', './templates/**/*.twig', './*/*/.js'],
	theme: {
		extend: {
			aspectRatio: {
				'16/9': '16/9',
				'3/2': '3/2',
				'4/3': '4/3',
				'3/4': '3/4',
				'1/1': '1/1',
			},
			colors: {
				white: '#FFFFFF',
				black: '#000000',
				wfac: {
					white: '#FFFFFF',
					sand: '#E2CFB7',
					cream: '#F0F0EE',
					ivory: '#F0EDE0',
					'dark-red': '#3C0C0B',
					'deep-red': '#D34C2E',
					'accent-red': '#FD614A',
					'accent-green': '#47B67F',
					green: '#22C55E',
					'deep-green': '#4B7A5F',
					'dark-green': '#305040',
				},
			},
			fontFamily: {
				heading: ['Freoarts Sans', 'sans-serif'],
				body: ['Inter', 'sans-serif'],
			},

			screens: {
				sm: '640px',
				md: '768px',
				lg: '1024px',
				xl: '1280px',
				'2xl': '1435px',
				'3xl': '1690px',
				'4xl': '2000px',
			},
			transitionDuration: {
				400: '400ms',
			},
			transitionTimingFunction: {
				fancy: 'cubic-bezier(0.76, 0, 0.24, 1)',
			},
		},
	},
	plugins: [require('@tailwindcss/typography')],
}
