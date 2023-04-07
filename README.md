# Page Style Inheritance

An example style is provided with the plugin, but it is meant to be filtered with styles applicable to the theme.

```php
add_filter( 'psi_styles', 'my_psi_styles_filter' );
/**
 * Filter Page Style Inheritance styles.
 *
 * @return array A custom list of style options.
 */
function my_psi_styles_filter() {
	return [
		''        => [
			'name' => __( 'Inherit parent style', 'my-plugin' ),
		],
		'my-style-one' => [
			'name'      => __( 'My Style One', 'my-plugin' ),
			'variables' => [
				'--page-style-color' => '#132324',
			],
			'selectors' => [
				'.example-selector' => [
					'color'            => 'var(--wp--preset--color--primary)',
					'background-color' => 'var(--wp--preset--color--secondary)',
				],
			],
		],
		'my-style-two' => [
			'name'      => __( 'My Style Two', 'my-plugin' ),
			'variables' => [
				'--page-style-color' => '#432123',
			],
			'selectors' => [
				'.example-selector' => [
					'color'            => 'var(--wp--preset--color--secondary)',
					'background-color' => 'var(--wp--preset--color--primary)',
				],
			],
		],
	];
}
```
