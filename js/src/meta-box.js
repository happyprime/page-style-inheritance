import { SelectControl } from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

const options = window.pageStyleInheritance;

const PageStyleInheritanceMetaBox = () => {
	const postType = wp.data.select( 'core/editor' ).getCurrentPostType();
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );
	const { psi_page_style } = meta;

	return (
		<PluginDocumentSettingPanel
			title={ __( 'Page style inheritance', 'page-style-inheritance' ) }
		>
			<SelectControl
				label={ __( 'Select page style', 'page-style-inhertiance' ) }
				value={ psi_page_style }
				onChange={ ( value ) =>
					setMeta( {
						...meta,
						psi_page_style: value,
					} )
				}
				options={ options }
				hideCancelButton={ true }
			/>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'page-style-inheritance-meta-box', {
	render: PageStyleInheritanceMetaBox,
	icon: 'admin-post',
} );
